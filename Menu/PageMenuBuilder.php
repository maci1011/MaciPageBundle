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

		$menu->addChild($this->translator->getText('menu.list', 'List'), array('route' => 'maci_list'));

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

    public function createTermsMenu(array $options)
	{
		$menu = $this->factory->createItem('root');

		$menu->setChildrenAttribute('class', 'nav');

		$menu->addChild($this->translator->getText('menu.terms.privacy', 'Privacy Policy'), array('route' => 'maci_page', 'routeParameters' => array('path' => 'privacy')));

		$menu->addChild($this->translator->getText('menu.terms.cookie', 'Cookie Policy'), array('route' => 'maci_page', 'routeParameters' => array('path' => 'cookie')));

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
			if (!$page->getPath() || $page->getPath() === 'homepage' || $page->getPath() === 'contacts' || preg_match(':Terms:', $page->getTemplate()) ) {
				continue;
			}
			$title = $page->getTitle();
			$menu->addChild($title, array(
			    'route' => 'maci_page',
			    'routeParameters' => array('path' => $page->getPath())
			));
			if ($children) {
				$this->addChildren($menu[$title], $page);
			}
		}
	}

    public function addChildren($menu, $item, $dropdown = false)
	{
		if (count($item->getCurrentChildren())) {
			$menu->setChildrenAttribute('class', 'nav');
			if ($dropdown) {
				$menu->setAttribute('dropdown', true);
			}
			foreach ($item->getCurrentChildren() as $child) {
				$menu->addChild($child->getTitle(), array(
				    'route' => 'maci_page',
				    'routeParameters' => array('path' => $child->getPath())
				));
				$this->addChildren($menu[$child->getTitle()], $child);
			}
		}
	}
}
