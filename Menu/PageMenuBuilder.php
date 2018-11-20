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

		$menu->setChildrenAttribute('class', 'nav navbar-nav navbar-right');

		$menu->addChild($this->translator->getText('menu.home', 'Home'), array('route' => 'maci_homepage'));

		$menu->addChild($this->translator->getText('menu.about', 'About'), array('route' => 'maci_page', 'routeParameters' => array('path' => 'about')));

		$menu->addChild($this->translator->getText('menu.gallery', 'Gallery'), array('route' => 'maci_media_gallery'));

		$menu->addChild($this->translator->getText('menu.shop', 'Shop'), array('route' => 'maci_product'));

		$menu->addChild($this->translator->getText('menu.blog', 'Blog'), array('route' => 'maci_blog'));

		$menu->addChild($this->translator->getText('menu.contacts', 'Contacts'), array('route' => 'maci_page', 'routeParameters' => array('path' => 'contacts')));

		return $menu;
	}

    public function createLeftMenu(array $options)
	{
		$menu = $this->factory->createItem('root');

		$menu->setChildrenAttribute('class', 'nav');

		$menu->addChild($this->translator->getText('menu.home', 'Home'), array('route' => 'maci_homepage'));

		$this->addPages($menu, true);

		return $menu;
	}

    public function createPageLeftMenu(array $options)
	{
		$menu = $this->factory->createItem('root');

		$menu->setChildrenAttribute('class', 'nav');

		$page = ( isset($options['page']) ? $options['page'] : false );

		if (!$page) return $menu;

		$parent = ( $page->getParent() ? $page->getParent() : false );

		$gparent = ( $parent && $parent->getParent() ? $parent->getParent() : false );

		if ($page->hasCurrentChildren()) $this->threeLevel($menu, ( $parent ? $parent : $page ));

		else $this->threeLevel($menu, ( $gparent ? $gparent : ( $parent ? $parent : $page )));

		return $menu;
	}

    public function createTermsMenu(array $options)
	{
		$menu = $this->factory->createItem('root');

		$menu->setChildrenAttribute('class', 'nav');

		$menu->addChild($this->translator->getText('menu.terms.privacy', 'Privacy Policy'), array('route' => 'maci_page', 'routeParameters' => array('path' => 'privacy')));

		$menu->addChild($this->translator->getText('menu.terms.cookie', 'Cookie Policy'), array('route' => 'maci_page', 'routeParameters' => array('path' => 'cookies')));

		return $menu;
	}

    public function createContactsMenu(array $options)
	{
		$menu = $this->factory->createItem('root');

		$menu->setChildrenAttribute('class', 'nav');

		$menu->addChild($this->translator->getText('menu.contacts', 'Contacts'), array('route' => 'maci_page', 'routeParameters' => array('path' => 'maci_page', 'routeParameters' => array('path' => 'contacts'))));

		return $menu;
	}

    public function addPages($menu, $children = false)
	{
		$pages = $this->om->getRepository('MaciPageBundle:Page')->findBy(array('parent' => null, 'removed' => false));
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
