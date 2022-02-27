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

	public function setVariantTypeAction(Request $request, $id)
	{
		if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->redirect('maci_homepage');
		}

		$om = $this->getDoctrine()->getManager();
		$item = $om->getRepository('MaciPageBundle:Shop\Product')->findOneById($id);

		if (!$item) {
			return $this->redirect($this->generateUrl('maci_product'));
		}

		$item->setVariantType($request->get('type'));
		$om->flush();

		return $this->redirect($this->generateUrl('maci_product_show', ['path' => $item->getPath()]));
	}

	public function addVariantAction(Request $request, $id)
	{
		if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->redirect('maci_homepage');
		}

		$om = $this->getDoctrine()->getManager();
		$item = $om->getRepository('MaciPageBundle:Shop\Product')->findOneById($id);

		if (!$item) {
			return $this->redirect($this->generateUrl('maci_product'));
		}

		$type = $request->get('type');
		$name = $request->get('name');
		$quantity = $request->get('quantity');

		if ($type == 'size') {
			$item->addSize(['name' => $name], $quantity);
			$om->flush();
		}

		return $this->redirect($this->generateUrl('maci_product_show', ['path' => $item->getPath()]));
	}
}
