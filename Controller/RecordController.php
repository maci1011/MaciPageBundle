<?php

namespace Maci\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf as Snappy;

class RecordController extends AbstractController
{
	public function importAction(Request $request)
	{
		if (!$this->isGranted('ROLE_ADMIN'))
			return $this->redirect($this->generateUrl('maci_homepage'));

		return $this->render('@MaciPage/Record/import.html.twig', [
			'debug' => !!$request->get('debug')
		]);
	}

	public function exportAction()
	{
		if (!$this->isGranted('ROLE_ADMIN'))
			return $this->redirect($this->generateUrl('maci_homepage'));

		return $this->render('@MaciPage/Record/export.html.twig');
	}

	public function loadUnsettedRecordsAction(Request $request)
	{
		if (!$this->isGranted('ROLE_ADMIN'))
			return $this->redirect($this->generateUrl('maci_homepage'));

		// --- Check Request

		if (!$request->isXmlHttpRequest())
			return $this->redirect($this->generateUrl('homepage'));

		if ($request->getMethod() !== 'POST')
			return new JsonResponse(['success' => false, 'error' => 'Bad Request.'], 200);

		// --- Check Auth

		$admin = $this->container->get(\Maci\AdminBundle\Controller\AdminController::class);
		if (!$admin->checkAuth())
			return new JsonResponse(['success' => false, 'error' => 'Not Authorized.'], 200);

		$cmd = $request->get('cmd', '');

		if (in_array($cmd, ['check_data', 'reset_data']))
			return new JsonResponse($this->checkData($cmd), 200);

		if ($cmd == 'version')
			return $this->updateVersion();

		$om = $this->getDoctrine()->getManager();
		$ids = $request->get('ids', []);
		$setId = $request->get('setId', 'null');
		$list = [];
		$_all = false;

		if (count($ids))
			$list = $om->getRepository('MaciPageBundle:Shop\Record')->findBy(['id' => $ids]);

		if (!count($list) && $setId != 'null')
		{
			$id = $request->get('setId');
			$set = $om->getRepository('MaciPageBundle:Shop\RecordSet')->findOneById(intval($id));
			if (!$set) return new JsonResponse(['success' => false, 'error' => 'Set not found.', 'id' => $id], 200);
			$list = $set->getChildren();
		}

		if (!count($list) && $cmd != '')
		{
			$_all = true;
			$list = $om->getRepository('MaciPageBundle:Shop\Record')->findAll();
		}

		if (!count($list))
			return new JsonResponse(['success' => false, 'error' => 'List is Empty.'], 200);

		if (in_array($cmd, ['reload_recs']))
			return $this->reloadRecords($list, $cmd);

		if ($_all)
			return new JsonResponse(['success' => false, 'error' => 'No Actions.'], 200);

		return $this->importList($list);
	}

	public function importList($list)
	{
		$om = $this->getDoctrine()->getManager();
		$imported = 0;
		$last = false;
		$addedpr = [];

		foreach ($list as $record)
		{
			$label = $record->getCode() . ' - ' . $record->getProductVariant();

			if (array_key_exists($label, $addedpr))
				$product = $addedpr[$label];

			else if ($last && $last->checkRecord($record))
				$product = $last;

			else $product = $this->getProduct($record);

			if (!$product)
			{
				$product = new \Maci\PageBundle\Entity\Shop\Product();
				$om->persist($product);
				$product->loadRecord($record);
				$addedpr[$record->getCode() . ' - ' . $record->getProductVariant()] = $product;
			}

			if (!$product->checkRecord($record) || !$product->importRecord($record))
				continue;

			$imported++;
			$last = $product;
		}

		$om->flush();

		return new JsonResponse([
			'success' => true,
			'length' => count($list),
			'imported' => $imported,
			'errors' => count($list) - $imported,
			'doubles' => $doubles
		], 200);
	}

	public function getProduct($record)
	{
		$om = $this->getDoctrine()->getManager();
		$product = false;
		$list = $om->getRepository('MaciPageBundle:Shop\Product')->findBy([
			'code' => $record->getCode()
		]);
		foreach ($list as $item)
		{
			if ($item->checkRecord($record) && !$product)
			{
				$product = $item;
				break;
			}
		}
		return $product;
	}

	public function checkData($cmd)
	{
		$om = $this->getDoctrine()->getManager();

		$removedRecords = 0;
		$qtaErrProducts = [];
		$qtaChangedProducts = [];
		$doubleProducts = [];
		$noRecords = [];
		$noProducts = [];
		$loaded = 0;
		$imported = 0;
		$created = 0;
		$newProducts = [];
		$newButLoaded = [];
		$newButNotLoaded = [];
		$newButNotImported = [];

		$records = [];
		$list = $om->getRepository('MaciPageBundle:Shop\Record')->findAll();

		foreach ($list as $record)
		{
			if (!array_key_exists($record->getCode(), $records))
				$records[$record->getCode()] = [];

			if ($record->getQuantity() == 0 || !$record->getParent())
			{
				$om->remove($record);
				$removedRecords++;
				continue;
			}

			array_push($records[$record->getCode()], $record);
		}

		$products = [];
		$list = $om->getRepository('MaciPageBundle:Shop\Product')->findAll();

		foreach ($list as $product)
		{
			if (!array_key_exists($product->getCode(), $products))
				$products[$product->getCode()] = [];

			$id = $product->getVariantId();
			$id = $product->getCode() . ($id ? "//" . $id : '');

			if (!$product->checkTotalQuantity())
				array_push($qtaErrProducts, $id);

			$product->resetQuantity();

			array_push($products[$product->getCode()], $product);
		}

		foreach ($products as $code => $list)
		{
			if (!array_key_exists($code, $records))
			{
				array_push($noRecords, $code);
				continue;
			}

			foreach ($records[$code] as $key => $record)
			{
				$rid = $record->getVariantIdentifier();
				$rid = $record->getType() . ' - ' . $record->getCode() . ($rid ? "//" . $rid : '');

				$found = false;
				$double = false;
				foreach ($list as $product)
				{
					if (!$product->checkRecord($record))
						continue;

					if ($found)
					{
						array_push($doubleProducts, $rid);
						$records[$code][$key] = 2;
						$double = true;
						break;
					}
					else
						$found = $product;
				}

				if (!$found || $double)
					continue;

				$records[$code][$key] = 1;
				$product = $found;

				$b = $product->getBuyed();
				$q = $product->getQuantity();
				$s = $product->getSelled();

				$record->resetLoadedValue();

				if ($product->loadRecord($record))
					$loaded++;

				if ($product->importRecord($record))
					$imported++;
			}

			if ($b != $product->getBuyed() || $q != $product->getQuantity() || $s != $product->getSelled())
			{
				array_push($qtaChangedProducts, $rid);
				$om->flush();
			}
		}

		foreach ($records as $code => $list)
		{
			$newprs = [];
			$news = [];

			foreach ($list as $key => $record)
			{
				if (!is_object($record))
					continue;
				
				$rid = $record->getVariantIdentifier();
				$rid = $record->getType() . ' - ' . $record->getCode() . ($rid ? "//" . $rid : '');

				array_push($noProducts, $rid);

				$product = false;

				foreach ($newprs as $new)
				{
					if ($new->checkRecord($record))
					{
						$product = $new;
						break;
					}
				}

				if ($record->isLoaded())
					array_push($newButLoaded, $rid);

				$record->resetLoadedValue();

				if (!$product)
				{
					$product = new \Maci\PageBundle\Entity\Shop\Product();
					$om->persist($product);

					if (!$product->loadRecord($record))
						array_push($newButNotLoaded, $rid);

					array_push($newprs, $product);
					array_push($news, $product->getVariantId());
					$created++;
				}

				foreach ($list as $i => $item)
				{
					if (!is_object($item) || $item->getId() == $record->getId())
						continue;
					
					$iid = $item->getVariantIdentifier();
					$iid = $item->getType() . ' - ' . $item->getCode() . ($iid ? "//" . $iid : '');

					if ($product->checkRecord($item) && $item->isPurchase())
					{
						$item->resetLoadedValue();

						if (!$product->importRecord($item))
							array_push($newButNotImported, $iid);

						$list[$key] = '3';
					}
				}

				if (!$product->importRecord($record))
					array_push($newButNotImported, $rid);
			}

			if (!array_key_exists($product->getCode(), $newProducts))
				$newProducts[$product->getCode()] = [];

			$newProducts[$code] = count($news) ? $news : false;
		}

		if ($cmd == 'reset_data')
			$om->flush();

		return [
			'success' => true,
			'removedRecords' => $removedRecords,
			'qtaErrProducts' => $qtaErrProducts,
			'qtaChangedProducts' => $qtaChangedProducts,
			'noRecords' => $noRecords,
			'noProducts' => $noProducts,
			'doubleProducts' => $doubleProducts,
			'newButLoaded' => $newButLoaded,
			'newButNotLoaded' => $newButNotLoaded,
			'newButNotImported' => $newButNotImported,
			'newProducts' => $newProducts,
			'loaded' => $loaded,
			'imported' => $imported,
			'created' => $created
		];
	}

	public function reloadRecords($list, $cmd)
	{
		$om = $this->getDoctrine()->getManager();
		$reload = 0;

		foreach ($list as $record)
		{
			if ($record->reload())
				$reload++;
		}

		$om->flush();

		return new JsonResponse([
			'success' => true,
			'length' => count($list),
			'reload' => $reload
		], 200);
	}

	public function updateVersion()
	{
		$om = $this->getDoctrine()->getManager();

		// Start Code Updates

		// $products = $om->getRepository('MaciPageBundle:Shop\Product')->findAll();

		// foreach ($products as $product)
		// {
		// }

		// $om->flush();

		// End Code Updates

		return new JsonResponse([
			'success' => true
		], 200);
	}

	public function exportRecordAction(Request $request)
	{
		if (!$this->isGranted('ROLE_ADMIN'))
			return $this->redirect($this->generateUrl('maci_homepage'));

		// --- Check Request

		if (!$request->isXmlHttpRequest())
			return $this->redirect($this->generateUrl('homepage'));

		$barcode = $request->get('barcode');

		if ($request->getMethod() !== 'POST' || !$barcode)
			return new JsonResponse(['success' => false, 'error' => 'Bad Request.'], 200);

		// --- Check Auth

		$admin = $this->container->get(\Maci\AdminBundle\Controller\AdminController::class);
		if (!$admin->checkAuth())
			return new JsonResponse(['success' => false, 'error' => 'Not Authorized.'], 200);

		$om = $this->getDoctrine()->getManager();
		$record = $om->getRepository('MaciPageBundle:Shop\Record')->findOneBy(['barcode' => $barcode, 'type' => 'purchas']);

		if (!$record)
			return new JsonResponse(['success' => false, 'error' => 'Record not Found.'], 200);

		$product = $product = $this->getProduct($record);

		if (!$product)
			return new JsonResponse(['success' => false, 'error' => 'Product not Found.'], 200);

		$type = $request->get('type');
		$quantiy = intval($request->get('quantity', 1));
		$newRecord = false;

		switch ($type)
		{
			case 'sale':
				$newRecord = $product->exportSaleRecord($record->getVariant(), $quantiy);
				break;

			case 'return':
				$newRecord = $product->exportReturnRecord($record->getVariant(), $quantiy);
				break;

			case 'back':
				$newRecord = $product->exportBackRecord($record->getVariant(), $quantiy);
				break;

			// case 'purchas':
			// 	$newRecord = $product->exportPurchaseRecord($record->getVariant(), $quantiy);
			// 	break;

			case 'quantity':
				return new JsonResponse([
					'success' => true,
					'variant' => $record->getVariantLabel(),
					'quantity' => $product->getQuantity($record->getVariant()),
					'code' => $product->getCode(),
					'tot' => $product->getQuantity(),
					'type' => $product->getType()
				], 200);

			case 'check':
				return new JsonResponse($this->checkProduct($product), 200);

			default:
				break;
		}

		if (!$newRecord) return new JsonResponse(['success' => false, 'error' => 'Export Failed.'], 200);

		$newRecord->setBarcode($record->getBarcode());
		$om->persist($newRecord);
		$om->flush();

		return new JsonResponse([
			'success' => true,
			'id' => $newRecord->getId(),
			'variant' => $newRecord->getVariantLabel(),
			'quantity' => $product->getQuantity($record->getVariant())
		], 200);
	}

	public function getLabelsAction(Request $request, $template = false)
	{
		if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->redirect($this->generateUrl('maci_homepage'));
		}

		$setId = $request->get('setId');

		if (!$setId) {
			$request->getSession()->getFlashBag()->add('danger', 'Bad Request.');
			return $this->redirect($this->generateUrl('maci_record_labels'));
		}

		$om = $this->getDoctrine()->getManager();
		$records = $om->getRepository('MaciPageBundle:Shop\Record')->findBy(['parent' => $setId]);
		$products = [];
		$last = false;

		foreach ($records as $record)
		{
			if ($last && $last->checkRecord($record)) $product = $last;
			else $product = $this->getProduct($record);

			if (!$product)
			{
				$message = "Product with code " . $record->getCode() . " and variant '" . $record->getVariantLabel() . "' not found.";
				echo $message;
				die();
			}

			$products[count($products)] = $product;
		}

		$snappy = new Snappy($this->container->getParameter('knp_snappy.pdf.binary'));

		$defaults = [
			'orientation' => 'portrait',
			'enable-javascript' => true,
			'javascript-delay' => 1000,
			'no-stop-slow-scripts' => true,
			'no-background' => false,
			'lowquality' => false,
			'margin-top'  => 0,
			'margin-right'  => 0,
			'margin-bottom'  => 0,
			'margin-left'  => 0,
			'encoding' => 'utf-8',
			'images' => true,
			'cookie' => array(),
			'dpi' => 300,
			'enable-external-links' => true,
			'enable-internal-links' => true
		];

		if ($template == 'report')
		{
			$list = [];
			$i = 0;
			$qta = 0;
			foreach ($records as $record)
			{
				$label = $record->getCode() . '-' . $products[$i]->getVariant();
				$variant = (1 < $record->getQuantity() ? $record->getQuantity() . ' ' : '') . $record->getVariantName();
				if (strtolower($variant) == 'simple') $variant = '-';
				if (array_key_exists($label, $list))
				{
					$x = $list[$label]['quantity'];
					$list[$label]['quantity'] = $x + $record->getQuantity();
					$x = $list[$label]['variants'];
					$list[$label]['variants'] = $x . ', ' . $variant;
				}
				else
				{
					$list[$label] = [
						'code' => $record->getCode(),
						'category' => $record->getCategory(),
						'quantity' => $record->getQuantity(),
						'variant' => $products[$i]->getVariant(),
						'variants' => $variant,
						'price' => $products[$i]->getPriceLabel()
					];
				}
				$qta += $record->getQuantity();
				$i++;
			}
			$defaults['page-size'] = 'A4';
			return new PdfResponse(
				$snappy->getOutputFromHtml($this->renderView('@MaciPage/Record/report_pdf.html.twig', [
					'list' => $list,
					'products' => $products,
					'qta' => $qta
				]), $defaults),
				'report.pdf'
			);
		}

		$defaults['page-height'] = 25;
		$defaults['page-width'] = 50;

		return new PdfResponse(
			$snappy->getOutputFromHtml($this->renderView('@MaciPage/Record/labels_pdf.html.twig', [
				'list' => $records,
				'products' => $products
			]), $defaults),
			'labels.pdf'
		);
	}

	public function checkProduct($product)
	{
		if (!$product)
			return ['success' => false];

		$om = $this->getDoctrine()->getManager();

		$loaded = 0;
		$imported = 0;
		$addedpr = [];
		$nfs = [];
		$errors = [];
		$resets = [];
		$reset_pr = [];

		$list = $om->getRepository('MaciPageBundle:Shop\Record')->findBy([
			'code' => $product->getCode()
		]);

		$edited = false;
		$b = $product->getBuyed();
		$q = $product->getQuantity();
		$s = $product->getSelled();

		$product->resetQuantity();

		foreach ($list as $record)
		{
			if (!$product->checkRecord($record))
				continue;

			$record->resetLoadedValue();

			if ($product->loadRecord($record))
				$loaded++;

			if ($product->importRecord($record))
				$imported++;
		}

		if ($b != $product->getBuyed() || $q != $product->getQuantity() || $s != $product->getSelled())
		{
			$edited = true;
			$om->flush();
		}

		return [
			'success' => true,
			'edited' => $edited,
			'loaded' => $loaded,
			'imported' => $imported,
			'variant' => $product->getVariant(),
			'quantity' => $product->getQuantity($record->getVariant()),
			'code' => $product->getCode(),
			'tot' => $product->getQuantity(),
			'type' => $product->getType()
		];
	}
}
