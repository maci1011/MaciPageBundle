<?php

namespace Maci\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Maci\AdminBundle\MaciPager as Pager;

class ShopController extends AbstractController
{
	public function indexAction(Request $request)
	{
		return $this->render('MaciPageBundle:Shop:index.html.twig', [
			'pager' => $this->getPager($request, $this->getDoctrine()->getManager()
				->getRepository('MaciPageBundle:Shop\Product')->getList()
			)
		]);
	}

	public function categoryAction(Request $request, $path)
	{
		$om = $this->getDoctrine()->getManager();
		$category = $om->getRepository('MaciPageBundle:Shop\Category')
			->findOneByPath($path);
		
		if (!$category)
		{
			if ($path == 'promo') return $this->render('MaciPageBundle:Shop:index.html.twig', [
				'list' => $this->getDoctrine()->getManager()
					->getRepository('MaciPageBundle:Shop\Product')->getPromo()
			]);

			return $this->redirect($this->generateUrl('maci_product'));
		}

		$list = $om->getRepository('MaciPageBundle:Shop\Product')
			->getByCategory($category);

		return $this->render('MaciPageBundle:Shop:category.html.twig', [
			'category' => $category,
			'pager' => $this->getPager($request, $list)
		]);
	}

	public function getPager($request, $list)
	{
		return new Pager($list, 32, 5, intval($request->get('p', 1)));
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
				'public' => true,
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
			return $this->redirect($this->generateUrl('maci_homepage'));
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
		if (!$this->isGranted('ROLE_ADMIN'))
			return $this->redirect($this->generateUrl('maci_homepage'));

		$om = $this->getDoctrine()->getManager();
		$item = $om->getRepository('MaciPageBundle:Shop\Product')->findOneById($id);

		if (!$item)
			return $this->redirect($this->generateUrl('maci_product'));

		$type = $request->get('type');
		$name = $request->get('name');
		$quantity = $request->get('quantity');

		if ($type == 'size')
		{
			$item->addSize(['name' => $name], $quantity);
			$om->flush();
		}

		return $this->redirect($this->generateUrl('maci_product_show', ['path' => $item->getPath()]));
	}

	public function setFitAction(Request $request, $id, $fit)
	{
		if (!$this->isGranted('ROLE_ADMIN'))
			return $this->redirect($this->generateUrl('maci_homepage'));

		$om = $this->getDoctrine()->getManager();
		$product = $om->getRepository('MaciPageBundle:Shop\Product')->findOneById($id);

		if (!$product)
			return $this->redirect($this->generateUrl('maci_product'));

		$product->setFit(in_array($fit, ['s', 'm', 'l']) ? $fit : 'm');
		$om->flush();

		return $this->redirect($this->generateUrl('maci_product_show', ['path' => $product->getPath()]));
	}

	public function lastProductsAction(Request $request)
	{
		return $this->render('@MaciPage/Shop/last_products.html.twig', [
			'list' => $this->getDoctrine()->getManager()->getRepository('MaciPageBundle:Shop\Product')
				->getLatestProducts(4)
		]);
	}

	public function lastProductsMailAction(Request $request)
	{
		return $this->render('@MaciPage/MailSlides/_products_list.html.twig', [
			'list' => $this->getDoctrine()->getManager()->getRepository('MaciPageBundle:Shop\Product')
				->getLatestProducts(6)
		]);
	}
}
