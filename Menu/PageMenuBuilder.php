<?php

namespace Maci\PageBundle\Menu;

use Knp\Menu\FactoryInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Maci\TranslatorBundle\Controller\TranslatorController;

class PageMenuBuilder
{
	private $factory;

	private $om;

	private $translator;

	private $locales;

	public function __construct(FactoryInterface $factory, ObjectManager $objectManager, TranslatorController $tc)
	{
		$this->factory = $factory;
		$this->om = $objectManager;
		$this->translator = $tc;
		$this->locales = $tc->getLocales();
	}

	public function createMainMenu(array $options)
	{
		$menu = $this->factory->createItem('root');

		$menu->setChildrenAttribute('class', 'navbar-nav');

		$menu->addChild($this->translator->getMenu('page.home', 'Home'), array('route' => 'maci_homepage'));

		$menu->addChild($this->translator->getMenu('page.about', 'About'), ['route' => 'maci_page', 'routeParameters' => ['path' => 'about']]);

		$menu->addChild($this->translator->getMenu('page.gallery', 'Gallery'), array('route' => 'maci_media_gallery'));

		$menu->addChild($this->translator->getMenu('page.shop', 'Shop'), array(
			'route' => 'maci_product',
			'extras' => [
				'routes' => [
					['route' => 'maci_product_category'],
					['route' => 'maci_product_show']
				],
			]
		));

		$menu->addChild($this->translator->getMenu('page.blog', 'Blog'), array(
			'route' => 'maci_blog',
			'extras' => [
				'routes' => [
					['route' => 'maci_blog_tag'],
					['route' => 'maci_blog_show']
				],
			]
		));

		$menu->addChild(
			$this->translator->getMenu('page.contacts', 'Contacts'),
			['route' => 'maci_page', 'routeParameters' => [
				'path' => $this->translator->getRoute('page.contacts', 'contacts')
			]]
		);

		return $menu;
	}

	public function createLeftMenu(array $options)
	{
		$menu = $this->factory->createItem('root');

		$menu->setChildrenAttribute('class', 'nav flex-column');

		$this->addPages($menu, true);

		return $menu;
	}

	public function createPageLeftMenu(array $options)
	{
		$menu = $this->factory->createItem('root');

		$menu->setChildrenAttribute('class', 'nav flex-column');

		$page = ( isset($options['page']) ? $options['page'] : false );

		if (!$page) return $menu;

		$parent = ( $page->getParent() ? $page->getParent() : false );

		$gparent = ( $parent && $parent->getParent() ? $parent->getParent() : false );

		if ($page->hasCurrentChildren()) $this->threeLevel($menu, ( $parent ? $parent : $page ));

		else $this->threeLevel($menu, ( $gparent ? $gparent : ( $parent ? $parent : $page )));

		return $menu;
	}

	public function createCorporateMenu(array $options)
	{
		$menu = $this->factory->createItem('root');

		$menu->setChildrenAttribute('class', 'nav flex-column');

		$menu->addChild($this->translator->getMenu('page.about', 'About'), ['route' => 'maci_page', 'routeParameters' => ['path' => 'about']]);

		return $menu;
	}

	public function createPrivacyMenu(array $options)
	{
		$menu = $this->factory->createItem('root');

		$menu->setChildrenAttribute('class', 'nav flex-column');

		$menu->addChild($this->translator->getMenu('terms.privacy', 'Privacy Policy'), ['route' => 'maci_page', 'routeParameters' => ['path' => 'privacy']]);

		$menu->addChild($this->translator->getMenu('terms.cookie', 'Cookie Policy'), ['route' => 'maci_page', 'routeParameters' => ['path' => 'cookies']]);

		return $menu;
	}

	public function createTermsMenu(array $options)
	{
		if ($this->translator->getCurrentLocale() == 'it')
			return $this->createTermsMenuIT($options);

		return $this->createTermsMenuEN($options);
	}

	public function createTermsMenuIT(array $options)
	{
		$menu = $this->factory->createItem('root');

		$menu->setChildrenAttribute('class', 'nav flex-column');

		$menu->addChild(
			$this->translator->getMenu('page.contacts', 'Contatti'),
			['route' => 'maci_page', 'routeParameters' => [
				'path' => $this->translator->getRoute('page.contacts', 'contatti')
			]]
		);

		$menu->addChild($this->translator->getMenu('terms.shipping', 'Spedizione'), ['route' => 'maci_page', 'routeParameters' => ['path' => 'spedizione']]);

		$menu->addChild($this->translator->getMenu('terms.payments', 'Pagamenti'), ['route' => 'maci_page', 'routeParameters' => ['path' => 'pagamenti']]);

		$menu->addChild($this->translator->getMenu('terms.returns-and-refunds', 'Resi e Rimbrsi'), ['route' => 'maci_page', 'routeParameters' => ['path' => 'resi-e-rimborsi']]);

		$menu->addChild($this->translator->getMenu('terms.size-guide', 'Guida alle Taglie'), ['route' => 'maci_page', 'routeParameters' => ['path' => 'guida-alle-taglie']]);

		return $menu;
	}

	public function createTermsMenuEN(array $options)
	{
		$menu = $this->factory->createItem('root');

		$menu->setChildrenAttribute('class', 'nav flex-column');

		$menu->addChild(
			$this->translator->getMenu('page.contacts', 'Contacts'),
			['route' => 'maci_page', 'routeParameters' => [
				'path' => $this->translator->getRoute('page.contacts', 'contacts')
			]]
		);

		$menu->addChild($this->translator->getMenu('terms.shipping', 'Shipping'), ['route' => 'maci_page', 'routeParameters' => ['path' => 'shipping']]);

		$menu->addChild($this->translator->getMenu('terms.payments', 'Payments'), ['route' => 'maci_page', 'routeParameters' => ['path' => 'payments']]);

		$menu->addChild($this->translator->getMenu('terms.returns-and-refunds', 'Returns And Refunds'), ['route' => 'maci_page', 'routeParameters' => ['path' => 'returns-and-refunds']]);

		$menu->addChild($this->translator->getMenu('terms.size-guide', 'Size Guide'), ['route' => 'maci_page', 'routeParameters' => ['path' => 'size-guide']]);

		return $menu;
	}

	public function addPages($menu, $children = false)
	{
		$pages = $this->om->getRepository('MaciPageBundle:Page\Page')->findBy(array('parent' => null, 'removed' => false));
		foreach ($pages as $page) {
			if (!$page->getPath() || $page->getPath() === 'homepage' || $page->getPath() === 'contacts' || $page->getPath() === 'contacts_success' || preg_match(':Terms:', $page->getTemplate()) ) {
				continue;
			}
			$title = $page->getMenuLabel();
			$menu->addChild($title, array(
				'route' => 'maci_page',
				'routeParameters' => array('path' => $page->getPath())
			));
			if ($children) {
				$this->addChildren($menu[$title], $page);
			}
		}
	}

	public function addChildren($menu, $item, $dropdown = false, $rec = false)
	{
		if (count($item->getCurrentChildren())) {
			$menu->setChildrenAttribute('class', 'nav');
			if ($dropdown) {
				$menu->setAttribute('dropdown', true);
			}
			foreach ($item->getCurrentChildren() as $child) {
				$menu->addChild($child->getMenuLabel(), array(
					'route' => 'maci_page',
					'routeParameters' => array('path' => $child->getPath())
				));
				if ($rec) $this->addChildren($menu[$child->getMenuLabel()], $child, $dropdown, true);
			}
		}
	}

	public function threeLevel($menu, $ancestor)
	{
		$menu->addChild($ancestor->getMenuLabel(), array(
			'route' => 'maci_page',
			'routeParameters' => array('path' => $ancestor->getPath(), '_locale' => $ancestor->getLocale())
		));

		if (count($ancestor->getCurrentChildren())) {

			$menu[$ancestor->getMenuLabel()]->setChildrenAttribute('class', 'nav');

			foreach ($ancestor->getCurrentChildren() as $child) {

				$menu[$ancestor->getMenuLabel()]->addChild($child->getMenuLabel(), array(
					'route' => 'maci_page',
					'routeParameters' => array('path' => $child->getPath(), '_locale' => $child->getLocale())
				));

				if (count($child->getCurrentChildren())) {

					$menu[$ancestor->getMenuLabel()][$child->getMenuLabel()]->setChildrenAttribute('class', 'nav');

					foreach ($child->getCurrentChildren() as $gchild) {

						$menu[$ancestor->getMenuLabel()][$child->getMenuLabel()]->addChild($gchild->getMenuLabel(), array(
							'route' => 'maci_page',
							'routeParameters' => array('path' => $gchild->getPath(), '_locale' => $gchild->getLocale())
						));

					}

				}

			}

		}

	}
}
