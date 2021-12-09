<?php

namespace Maci\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf as Snappy;

class ShopController extends AbstractController
{
	public function indexAction()
	{
		return $this->render('MaciPageBundle:Shop:index.html.twig', array(
			'list' => $this->getDoctrine()->getManager()
				->getRepository('MaciPageBundle:Shop\Product')->getList()
		));
	}

	public function categoryAction($path)
	{

		$category = $this->getDoctrine()->getManager()
			->getRepository('MaciPageBundle:Shop\Category')
			->findOneBy(array(
				'path' => $path
			));

		if (!$category) {
			return $this->redirect($this->generateUrl('maci_product'));
		}

		return $this->render('MaciPageBundle:Shop:category.html.twig', array(
			'category' => $category
		));
	}

	public function showAction($path)
	{
		$om = $this->getDoctrine()->getManager();
		$item = $om->getRepository('MaciPageBundle:Shop\Product')->getByPath($path);

		if (!$item) {
			return $this->redirect($this->generateUrl('maci_product'));
		}

		$variants = [];

		if ($item->getVariant() != null) {
			$variants = $om->getRepository('MaciPageBundle:Shop\Product')->findBy([
				'code' => $item->getCode(),
				'removed' => false
			]);
		}

		return $this->render('MaciPageBundle:Shop:show.html.twig', array(
			'item' => $item,
			'variants' => $variants
		));
	}

	public function importAction()
	{
		if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->redirect('maci_homepage');
		}
		return $this->render('@MaciPage/Shop/import.html.twig');
	}

	public function salesAction()
	{
		if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->redirect('maci_homepage');
		}
		return $this->render('@MaciPage/Shop/sales.html.twig');
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
		$list = $om->getRepository('MaciPageBundle:Shop\Record')->findBy(['product' => null]);

		if (!count($list)) {
			return new JsonResponse(['success' => true], 200);
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

	public function labelsAction()
	{
		if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->redirect('maci_homepage');
		}
		return $this->render('@MaciPage/Shop/labels.html.twig');
	}

	public function getLabelsAction()
	{
		if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->redirect('maci_homepage');
		}

		$binary = $this->container->getParameter('knp_snappy.pdf.binary');
		$snappy = new Snappy($binary);

		// $url = 'http://base.localhost' . $this->generateUrl('maci_product', array()); 
		$html = $this->renderView('@MaciPage/Shop/pdf_content.html.twig');

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
