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

		$om = $this->getDoctrine()->getManager();

		$debug = $request->get('debug');

		$ids = $request->get('ids');
		if (count($ids)) $list = $om->getRepository('MaciPageBundle:Shop\Record')->findBy(['id' => $ids]);
		else
		{
			$id = $request->get('setId');
			$set = $om->getRepository('MaciPageBundle:Shop\RecordSet')->findOneById(intval($id));
			if (!$set) return new JsonResponse(['success' => false, 'error' => 'Set not found.', 'id' => $id], 200);
			$list = $set->getChildren();
			if ($debug == "true")
			{
				$nfl = [];
				foreach ($list as $record) {
					$product = $om->getRepository('MaciPageBundle:Shop\Product')->findOneBy([
						'code' => $record->getCode(),
						'variant' => $record->getProductVariant()
					]);
					if (!$product)
					{
						if ($record->isLoaded()) $record->resetLoadedValue();
						$nfl[count($nfl)] = $record->getCode() . ' - ' . $record->getVariantLabel();
					}
				}
				$om->flush();
				return new JsonResponse(['success' => true, 'notFounds' => count($nfl), 'list' => $nfl], 200);
			}
		}

		if (!count($list)) {
			return new JsonResponse(['success' => false, 'error' => 'List is Empty.'], 200);
		}

		$last = false;
		foreach ($list as $record)
		{
			if ($last && $last->checkRecord($record)) $product = $last;
			else $product = $om->getRepository('MaciPageBundle:Shop\Product')->findOneBy([
					'code' => $record->getCode(),
					'variant' => $record->getProductVariant()
				]);

			if (!$product)
			{
				$product = new \Maci\PageBundle\Entity\Shop\Product();
				$om->persist($product);
			}

			$product->importRecord($record);
			$last = $product;
		}

		$om->flush();

		return new JsonResponse(['success' => true], 200);
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
		$newRecord = false;

		switch ($type) {
			case 'sale':
				$newRecord = $product->exportSaleRecord($record->getVariant());
				break;

			case 'return':
				$newRecord = $product->exportReturnRecord($record->getVariant());
				break;

			case 'purchas':
				$newRecord = $product->exportPurchaseRecord($record->getVariant());
				break;

			case 'quantity':
				return new JsonResponse(['success' => true, 'variant' => $record->getVariantLabel(), 'quantity' => $product->getQuantity($record->getVariant())], 200);
				break;

			default:
				break;
		}

		if (!$newRecord) return new JsonResponse(['success' => false, 'error' => 'Export Failed.'], 200);

		$newRecord->setBarcode($record->getBarcode());
		$om->persist($newRecord);
		$om->flush();

		return new JsonResponse(['success' => true, 'id' => $newRecord->getId(), 'variant' => $newRecord->getVariantLabel(), 'quantity' => $product->getQuantity($record->getVariant())], 200);
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
