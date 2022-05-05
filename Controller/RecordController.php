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
		if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->redirect($this->generateUrl('maci_homepage'));
		}
		return $this->render('@MaciPage/Record/import.html.twig', [
			'debug' => !!$request->get('debug')
		]);
	}

	public function exportAction()
	{
		if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->redirect($this->generateUrl('maci_homepage'));
		}
		return $this->render('@MaciPage/Record/export.html.twig');
	}

	public function loadUnsettedRecordsAction(Request $request)
	{
		if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->redirect($this->generateUrl('maci_homepage'));
		}

		// --- Check Request

		if (!$request->isXmlHttpRequest()) {
			return $this->redirect($this->generateUrl('homepage'));
		}

		if ($request->getMethod() !== 'POST') {
			return new JsonResponse(['success' => false, 'error' => 'Bad Request.'], 200);
		}

		// --- Check Auth

		$admin = $this->container->get(\Maci\AdminBundle\Controller\AdminController::class);
		if (!$admin->checkAuth()) {
			return new JsonResponse(['success' => false, 'error' => 'Not Authorized.'], 200);
		}

		$cmd = $request->get('cmd', '');

		if (in_array($cmd, ['check_qta', 'reset_qta']))
			return new JsonResponse($this->checkQuantity($cmd), 200);

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

		if (!count($list)) {
			return new JsonResponse(['success' => false, 'error' => 'List is Empty.'], 200);
		}

		if ($cmd == 'version') return $this->updateVersion($list);

		if (in_array($cmd, ['get_nf', 'reset_nf', 'reload_pr']))
			return $this->resetNotFounds($list, $cmd);

		if ($_all) return new JsonResponse(['success' => false, 'error' => 'No Actions.'], 200);

		return $this->importList($list);
	}

	public function importList($list)
	{
		$om = $this->getDoctrine()->getManager();
		$errors = 0;
		$last = false;
		$addedpr = [];

		foreach ($list as $record)
		{
			$label = $record->getCode() . ' - ' . $record->getProductVariant();
			if (array_key_exists($label, $addedpr)) $product = $addedpr[$label];
			else if ($last && $last->checkRecord($record)) $product = $last;
			else $product = $om->getRepository('MaciPageBundle:Shop\Product')->findOneBy([
					'code' => $record->getCode(),
					'variant' => $record->getProductVariant()
				]);

			if (!$product)
			{
				$product = new \Maci\PageBundle\Entity\Shop\Product();
				$om->persist($product);
				$product->loadRecord($record);
				$addedpr[$record->getCode() . ' - ' . $record->getProductVariant()] = $product;
			}

			if (!$product->checkRecord($record))
			{
				$errors++;
				continue;
			}

			if (!$product->importRecord($record)) $errors++;
			$last = $product;
		}

		$om->flush();

		return new JsonResponse([
			'success' => true,
			'errors' => $errors
		], 200);
	}

	public function checkQuantity($cmd, $products = false)
	{
		$om = $this->getDoctrine()->getManager();
		$loaded = 0;
		$imported = 0;
		$errors = [];
		$nor = []; // No Records
		$nz = []; // Not Zero

		if (!$products)
			$products = $om->getRepository('MaciPageBundle:Shop\Product')->findAll();

		foreach ($products as $product)
		{
			if (!$product->checkTotalQuantity())
				array_push($errors, $product->getCode() . ' - ' . $product->getVariant());

			$product->resetQuantity();

			$list = $om->getRepository('MaciPageBundle:Shop\Record')->findBy([
				'code' => $product->getCode()
			]);

			$label = $product->getCode() . ' - ' . $product->getVariant();

			if (!count($list)) 
			{
				array_push($nor, $label);
				continue;
			}

			foreach ($list as $record)
			{
				if (!$product->checkRecord($record)) continue;

				$record->resetLoadedValue();

				if ($product->loadRecord($record))
					$loaded++;

				if ($product->importRecord($record))
					$imported++;
			}

			if (!$product->checkTotalQuantity())
				array_push($nz, $label);
		}

		if ($cmd == 'reset_qta')
			$om->flush();

		return [
			'success' => true,
			'loaded' => $loaded,
			'imported' => $imported,
			'errors' => $errors,
			'nor' => $nor,
			'nz' => $nz
		];
	}

	public function resetNotFounds($list, $cmd)
	{
		$om = $this->getDoctrine()->getManager();
		$loaded = 0;
		$imported = 0;
		$addedpr = [];
		$nfs = [];
		$errors = [];
		$resets = [];

		foreach ($list as $record)
		{
			$product = $om->getRepository('MaciPageBundle:Shop\Product')->findOneBy([
				'code' => $record->getCode(),
				'variant' => $record->getProductVariant()
			]);

			$is_nf = !$product || !$product->checkRecordVariant($record);

			if ($is_nf)
			{
				array_push($nfs, $record->getCode() . ' - ' . $record->getVariantLabel());

				if (in_array($cmd, ['reset_nf', 'reload_pr']))
				{
					$record->resetLoadedValue();

					$label = $record->getCode() . ' - ' . $record->getProductVariant();

					if (!$product || !$product->checkRecord($record))
					{
						if (array_key_exists($label, $addedpr))
						{
							$product = $addedpr[$label];
						}
						else
						{
							$product = new \Maci\PageBundle\Entity\Shop\Product();

							if ($cmd == 'reload_pr')
								$om->persist($product);

							$addedpr[$label] = $product;
						}
					}

					if (!$product)
					{
						array_push($errors, $record->getCode() . ' - ' . $record->getVariantLabel());
						continue;
					}

					if ($product->loadRecord($record))
						$loaded++;

					if ($product->importRecord($record))
						$imported++;

					array_push($resets, $this->checkQuantity(
						$cmd == 'reload_pr' ? 'reset_qta' : 'check_qta', [$product]
					));
				}
			}
		}

		if ($cmd == 'reload_pr')
			$om->flush();

		return new JsonResponse([
			'success' => true,
			'not_founds' => $nfs,
			'addedpr' => count($addedpr),
			'loaded' => $loaded,
			'imported' => $imported,
			'resets' => $resets,
			'errors' => $errors
		], 200);
	}

	public function updateVersion($list)
	{
		$om = $this->getDoctrine()->getManager();
		$jumped = [];

		foreach ($list as $record)
		{
			$product = $om->getRepository('MaciPageBundle:Shop\Product')->findOneBy([
				'code' => $record->getCode(),
				'variant' => $record->getProductVariant()
			]);
			if (!$product)
			{
				array_push($jumped, $record->getId());
				continue;
			}
			// Start Code Updates

			// End Code Updates
		}

		$om->flush();

		return new JsonResponse([
			'success' => true,
			'len' => count($list),
			'versioned' => count($list) - count($jumped),
			'jumped' => $jumped
		], 200);
	}

	public function exportRecordAction(Request $request)
	{
		if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->redirect($this->generateUrl('maci_homepage'));
		}

		// --- Check Request

		if (!$request->isXmlHttpRequest()) {
			return $this->redirect($this->generateUrl('homepage'));
		}

		$barcode = $request->get('barcode');

		if ($request->getMethod() !== 'POST' || !$barcode) {
			return new JsonResponse(['success' => false, 'error' => 'Bad Request.'], 200);
		}

		// --- Check Auth

		$admin = $this->container->get(\Maci\AdminBundle\Controller\AdminController::class);
		if (!$admin->checkAuth()) {
			return new JsonResponse(['success' => false, 'error' => 'Not Authorized.'], 200);
		}

		$om = $this->getDoctrine()->getManager();
		$record = $om->getRepository('MaciPageBundle:Shop\Record')->findOneBy(['barcode' => $barcode, 'type' => 'purchas']);

		if (!$record) {
			return new JsonResponse(['success' => false, 'error' => 'Record not Found.'], 200);
		}

		$product = $om->getRepository('MaciPageBundle:Shop\Product')->findOneBy(['code' => $record->getCode(), 'variant' => $record->getProductVariant()]);

		if (!$product) {
			return new JsonResponse(['success' => false, 'error' => 'Product not Found.'], 200);
		}

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
					'quantity' => $product->getQuantity($record->getVariant())
				], 200);
				break;

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
			else $product = $this->getDoctrine()->getManager()
				->getRepository('MaciPageBundle:Shop\Product')->findOneBy([
					'code' => $record->getCode(),
					'variant' => $record->getProductVariant()
				]);

			if (!$product)
			{
				$products[count($products)] = false;
				continue;
			}

			$products[count($products)] = $product;
		}

		// var_dump($code);die();

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
}
