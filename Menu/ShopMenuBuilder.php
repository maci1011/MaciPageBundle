<?php

namespace Maci\PageBundle\Menu;

use Knp\Menu\FactoryInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Maci\TranslatorBundle\Controller\TranslatorController;

class ShopMenuBuilder
{
	private $factory;

	private $translator;

	private $locales;

	public function __construct(FactoryInterface $factory, ObjectManager $om, RequestStack $requestStack, TranslatorController $tc)
	{
		$this->factory = $factory;
		$this->om = $om;
		$this->request = $requestStack->getCurrentRequest();
		$this->translator = $tc;
		$this->locales = $tc->getLocales();
	}

	public function createCustomerServiceMenu(array $options)
	{
		$menu = $this->factory->createItem('root');

		$menu->setChildrenAttribute('class', 'nav flex-column');

		$menu->addChild($this->translator->getText('menu.shopping.customer_service', 'Customer Service'), array('route' => 'maci_page', 'routeParameters' => array('path' => 'customer-service')));

		$menu->addChild($this->translator->getText('menu.shopping.size', 'Size Guide'), array('route' => 'maci_page', 'routeParameters' => array('path' => 'size-guide')));

		$menu->addChild($this->translator->getText('menu.shopping.guide', 'Shopping Guide'), array('route' => 'maci_page', 'routeParameters' => array('path' => 'shopping-guide')));

		$menu->addChild($this->translator->getText('menu.shopping.shipping', 'Shipping'), array('route' => 'maci_page', 'routeParameters' => array('path' => 'shipping')));

		$menu->addChild($this->translator->getText('menu.shopping.payment', 'Payment'), array('route' => 'maci_page', 'routeParameters' => array('path' => 'payment')));

		$menu->addChild($this->translator->getText('menu.shopping.refunds', 'Returns And Refunds'), array('route' => 'maci_page', 'routeParameters' => array('path' => 'refunds')));

		return $menu;
	}

	public function createPolicyMenu(array $options)
	{
		$menu = $this->factory->createItem('root');

		$menu->setChildrenAttribute('class', 'nav flex-column');

		$menu->addChild($this->translator->getMenu('terms.gcs', 'General Conditions of Sale'), ['route' => 'maci_page', 'routeParameters' => ['path' => $this->translator->getRoute('page.gcs', 'general-conditions')]]);

		$menu->addChild($this->translator->getMenu('terms.privacy', 'Privacy Policy'), ['route' => 'maci_page', 'routeParameters' => ['path' => 'privacy']]);

		$menu->addChild($this->translator->getMenu('terms.cookie', 'Cookie Policy'), ['route' => 'maci_page', 'routeParameters' => ['path' => 'cookies']]);

		return $menu;
	}

	public function createMainMenu(array $options)
	{
		$menu = $this->factory->createItem('root');

		$menu->setChildrenAttribute('class', 'navbar-nav mr-auto');

		$this->addCategories($menu);

		return $menu;
	}

	public function createCategoriesMenu(array $options)
	{
		$menu = $this->factory->createItem('root');

		$menu->setChildrenAttribute('class', 'navbar-nav');

		$menu->addChild($this->translator->getMenu('page.shop.new-products', 'New Products'), ['route' => 'maci_product']);

		$this->addCategories($menu);

		$menu->addChild($this->translator->getMenu('page.shop.promo', 'Promo'), [
				'route' => 'maci_product_category',
				'routeParameters' => ['path' => 'promo']
		]);

		return $menu;
	}

	public function createSectionMenu(array $options)
	{
		$menu = $this->factory->createItem('root');

		$menu->setChildrenAttribute('class', 'nav flex-column');

		$menu->addChild($this->translator->getMenu('page.shop.new-products', 'New Products'), ['route' => 'maci_product']);

		$this->addCategories($menu);

		$menu->addChild($this->translator->getMenu('page.shop.promo', 'Promo'), [
				'route' => 'maci_product_category',
				'routeParameters' => ['path' => 'promo']
		]);

		$menu->addChild($this->translator->getMenu('page.cart', 'Cart'), ['route' => 'maci_order_cart']);

		return $menu;
	}

	public function createLeftMenu(array $options)
	{
		$menu = $this->factory->createItem('root');

		$menu->setChildrenAttribute('class', 'nav flex-column');

		$this->addCategories($menu);

		return $menu;
	}

	public function createContactsMenu(array $options)
	{
		$menu = $this->factory->createItem('root');

		$menu->setChildrenAttribute('class', 'nav flex-column');

		$menu->addChild($this->translator->getText('menu.contacts', 'Contacts'), array('route' => 'maci_page', 'routeParameters' => array('path' => 'maci_page', 'routeParameters' => array('path' => 'contacts'))));

		return $menu;
	}

	public function addCategories($menu)
	{
		$categories = $this->om->getRepository('MaciPageBundle:Shop\Category')->findBy(array(
			'locale' => $this->request->getLocale(),
			'removed' => false
		));

		foreach ($categories as $category) {

			$menu->addChild($category->getName(), array(
				'route' => 'maci_product_category',
				'routeParameters' => array('path' => $category->getPath())
			));

		}
	}
}
