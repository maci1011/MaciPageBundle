<?php

namespace Maci\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf as Snappy;

class RecordController extends AbstractController
{
	public function importAction()
	{
		if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->redirect('maci_homepage');
		}
		return $this->render('@MaciPage/Record/import.html.twig');
	}

	public function salesAction()
	{
		if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->redirect('maci_homepage');
		}
		return $this->render('@MaciPage/Record/sales.html.twig');
	}

	public function loadUnsettedRecordsAction(Request $request)
	{
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
		$list = $om->getRepository('MaciPageBundle:Shop\Record')->findBy(['id' => $request->get('ids')]);

		if (!count($list)) {
			return new JsonResponse(['success' => false, 'error' => 'List is Empty.'], 200);
		}

		$last = false;
		foreach ($list as $record)
		{
			if ($last && $last->getCode() == $record->getCode()) $product = $last;
			else $product = $this->getDoctrine()->getManager()
				->getRepository('MaciPageBundle:Shop\Product')->findOneBy([
					'code' => $record->getCode()
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

		$saleRecord = $product->exportSaleRecord($record->getVariant());

		if (!$saleRecord) {
			return new JsonResponse(['success' => false, 'error' => 'Export Failed.'], 200);
		}

		$om->persist($saleRecord);
		$om->flush();

		return new JsonResponse(['success' => true, 'id' => $saleRecord->getId(), 'variant' => $saleRecord->getVariantLabel()], 200);
	}

	public function labelsAction()
	{
		if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->redirect('maci_homepage');
		}
		return $this->render('@MaciPage/Record/labels.html.twig');
	}

	public function getLabelsAction()
	{
		if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->redirect('maci_homepage');
		}

		$binary = $this->container->getParameter('knp_snappy.pdf.binary');
		$snappy = new Snappy($binary);

		// $url = 'http://base.localhost' . $this->generateUrl('maci_product', array()); 
		$html = $this->renderView('@MaciPage/Record/pdf_content.html.twig');

		return new PdfResponse(
			$snappy->getOutputFromHtml($html, array(
				'orientation' => 'portrait',
				'enable-javascript' => true,
				'javascript-delay' => 1000,
				'no-stop-slow-scripts' => true,
				'no-background' => false,
				'lowquality' => false,
				'page-height' => 25,
				'page-width'  => 50,
				'margin-top'  => 4,
				'margin-right'  => 4,
				'margin-bottom'  => 4,
				'margin-left'  => 4,
				'encoding' => 'utf-8',
				'images' => true,
				'cookie' => array(),
				'dpi' => 300,
				'enable-external-links' => true,
				'enable-internal-links' => true
			)),
			'file.pdf'
		);
	}
}
