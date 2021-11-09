<?php

namespace Maci\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

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

		$product = $this->getDoctrine()->getManager()
			->getRepository('MaciPageBundle:Shop\Product')->getByPath($path);

		if (!$product) {
			return $this->redirect($this->generateUrl('maci_product'));
		}

		return $this->render('MaciPageBundle:Shop:show.html.twig', array(
			'item' => $product
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
}
