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
		$set = false;
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

		if ($cmd == 'reload_recs')
			return $this->reloadRecords($list, $cmd);

		if ($cmd == 'reset_import')
			return new JsonResponse($this->resetImport($set));

		if ($_all)
			return new JsonResponse(['success' => false, 'error' => 'No Actions.'], 200);

		return $this->importList($list);
	}

	public function importList($list)
	{
		$om = $this->getDoctrine()->getManager();
		$imported = 0;
		$lasts = [];
		$addedpr = [];

		foreach ($list as $record)
		{
			$product = $this->getProduct($record, $lasts);

			if (!$product)
			{
				$product = new \Maci\PageBundle\Entity\Shop\Product();
				$om->persist($product);
				$product->loadRecord($record);
				array_push($addedpr, $record->getCode() . ' - ' . $record->getProductVariant());
				array_push($lasts, $product);
			}

			if ($product->checkRecord($record) && $product->importRecord($record))
				$imported++;
		}

		$om->flush();

		return new JsonResponse([
			'success' => true,
			'length' => count($list),
			'imported' => $imported,
			'addedpr' => $addedpr,
			'errors' => count($list) - $imported
		], 200);
	}

	public function resetImport($set)
	{
		if (!$set)
			return ['success' => false, 'error' => 'No Set.'];

		$om = $this->getDoctrine()->getManager();
		$products = [];

		foreach ($set->getChildren() as $record)
		{
			$product = $this->getProduct($record, $products);
			$product->revertRecord($record);
			$om->remove($record);
		}

		$om->flush();

		return ['success' => true];
	}

	public function getProduct($record, &$list)
	{
		$product = false;
		foreach ($list as $item)
		{
			if ($item->checkRecord($record) && !$product)
			{
				$product = $item;
				break;
			}
		}
		if (!$product)
		{
			$product = $this->findProduct($record);
			if ($product)
				array_push($list, $product);
		}
		return $product;
	}

	public function findProduct($record)
	{
		$list = $this->getDoctrine()->getManager()->getRepository('MaciPageBundle:Shop\Product')->findBy([
			'code' => $record->getCode()
		]);
		$product = false;
		foreach ($list as $item)
		{
			if ($item->checkRecord($record))
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
		$qtas = [];

		$removedRecords = [];
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
				array_push($removedRecords, ($record->getId() . '-' . $record->getCode()));
				$om->remove($record);
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

			$b = $product->getBuyed();
			$q = $product->getQuantity();
			$s = $product->getSelled();

			$qtas['#'.$product->getId()] = [$b, $q, $s];

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

				$record->resetLoadedValue();

				if ($product->loadRecord($record))
					$loaded++;

				if ($product->importRecord($record))
					$imported++;
			}

			if (!array_key_exists('#'.$product->getId(), $qtas))
				continue;

			$qs = $qtas['#'.$product->getId()];

			if ($qs[0] != $product->getBuyed() || $qs[1] != $product->getQuantity() || $qs[2] != $product->getSelled())
			{
				array_push($qtaChangedProducts, $rid);
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

			if (count($news))
			{
				$newProducts[$code] = $news;
			}
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
		if ($request->getMethod() !== 'POST' || !$request->isXmlHttpRequest())
			return $this->redirect($this->generateUrl('homepage'));

		// --- Check Auth

		$admin = $this->container->get(\Maci\AdminBundle\Controller\AdminController::class);
		if (!$admin->checkAuth())
			return new JsonResponse(['success' => false, 'error' => 'Not Authorized.'], 200);

		// --- Check Request

		$barcode = $request->get('barcode');
		if (!$barcode)
			return new JsonResponse(['success' => false, 'error' => 'Bad Request.'], 200);

		$om = $this->getDoctrine()->getManager();
		$record = $om->getRepository('MaciPageBundle:Shop\Record')->findOneBy(['barcode' => $barcode, 'type' => 'purchas']);

		if (!$record)
			return new JsonResponse(['success' => false, 'error' => 'Record not Found.'], 200);

		$product = $this->findProduct($record);

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

	public function exportProductsAction(Request $request)
	{
		// --- Check Request

		if ($request->getMethod() !== 'POST' || !$request->isXmlHttpRequest())
			return $this->redirect($this->generateUrl('homepage'));

		// --- Check Auth

		$admin = $this->container->get(\Maci\AdminBundle\Controller\AdminController::class);
		if (!$admin->checkAuth())
			return new JsonResponse(['success' => false, 'error' => 'Not Authorized.'], 200);

		$om = $this->getDoctrine()->getManager();

		$setId = $request->get('setId');
		$set = $om->getRepository('MaciPageBundle:Shop\RecordSet')->findOneById(intval($setId));
		if (!$set)
			return new JsonResponse(['success' => false, 'error' => 'Set not found.', 'id' => $setId], 200);

		$products = $request->get('products');
		if (!$products)
			return new JsonResponse(['success' => false, 'error' => 'Bad Request.'], 200);

		$checks = [];
		$errors = [];

		foreach ($products as $key => $pdata)
		{
			if (intval($pdata['quantity']) == 0)
				continue;

			$product = $om->getRepository('MaciPageBundle:Shop\Product')
				->findOneBy(['id' => $pdata['id']]);

			if (!$product)
			{
				array_push($errors, ['error' => 'Record not Found.', 'id' => $pdata['id']]);
				continue;
			}

			$newRecord = false;

			switch ($pdata['type'])
			{
				case 'back':
					$newRecord = $product->exportBackRecord($pdata['variant'], intval($pdata['quantity']));
					break;

				case 'return':
					$newRecord = $product->exportReturnRecord($pdata['variant'], intval($pdata['quantity']));
					break;

				case 'sale':
					$newRecord = $product->exportSaleRecord($pdata['variant'], intval($pdata['quantity']));
					break;

				case 'check':
					array_push($checks, ['result' => $this->checkProduct($product), 'id' => $pdata['id']]);
					break;
			}

			if ($pdata['type'] == 'check')
				continue;

			if (!$newRecord)
			{
				array_push($errors, ['error' => 'Export Failed.', 'id' => $pdata['id']]);
				continue;
			}

			$newRecord->setParent($set);
			$om->persist($newRecord);
		}

		$om->flush();

		return new JsonResponse([
			'success' => true,
			'errors' => $errors,
			'checks' => $checks
		], 200);
	}

	public function getProductsListAction(Request $request)
	{
		// --- Check Request

		if ($request->getMethod() !== 'POST' || !$request->isXmlHttpRequest())
			return $this->redirect($this->generateUrl('homepage'));

		// --- Check Auth

		$admin = $this->container->get(\Maci\AdminBundle\Controller\AdminController::class);
		if (!$admin->checkAuth())
			return new JsonResponse(['success' => false, 'error' => 'Not Authorized.'], 200);

		$om = $this->getDoctrine()->getManager();

		$type = $request->get('type');

		switch ($type)
		{
			case 'olders':
				$list = $om->getRepository('MaciPageBundle:Shop\Product')->getOlders();
				break;
			
			case 'negatives':
				$list = $om->getRepository('MaciPageBundle:Shop\Product')->getNegatives();
				break;
			
			default:
				$list = [];
				break;
		}

		return new JsonResponse([
			'success' => true,
			'list' => $admin->getDataFromList($admin->getEntity('shop', 'product'), $list)
		], 200);
	}

	public function getLabelsAction(Request $request, $template = false)
	{
		if (!$this->isGranted('ROLE_ADMIN'))
			return $this->redirect($this->generateUrl('maci_homepage'));

		$setId = $request->get('setId');

		if (!$setId)
		{
			$request->getSession()->getFlashBag()->add('danger', 'Bad Request.');
			return $this->redirect($this->generateUrl('maci_record_labels'));
		}

		$om = $this->getDoctrine()->getManager();
		$records = $om->getRepository('MaciPageBundle:Shop\Record')->findBy(['parent' => $setId], ['code' => 'ASC']);
		$products = [];
		$lasts = [];

		foreach ($records as $record)
		{
			$product = $this->getProduct($record, $lasts);

			if (!$product)
			{
				echo "Product with code " . $record->getCode() . " and variant '" . $record->getVariantLabel() . "' not found.";
				die();
			}

			array_push($products, $product);
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
			'cookie' => [],
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
		}
		else
		{
			$product->setUpdatedValue();
		}

		$om->flush();

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
