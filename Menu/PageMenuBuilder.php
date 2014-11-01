<?php

namespace Maci\PageBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\Common\Persistence\ObjectManager;

class PageMenuBuilder
{
	private $factory;

	private $securityContext;

	private $user;

    private $om;

	public function __construct(FactoryInterface $factory, SecurityContext $securityContext, ObjectManager $om)
	{
	    $this->factory = $factory;
	    $this->securityContext = $securityContext;
	    $this->user = $securityContext->getToken()->getUser();
        $this->om = $om;
	}

    public function createMainMenu(Request $request)
	{
		$menu = $this->factory->createItem('root');

		$menu->setChildrenAttribute('class', 'nav navbar-nav');

		$menu->addChild('Home', array('route' => 'maci_homepage'));

		$pages = $this->om->getRepository('MaciPageBundle:Page')->findBy(array('parent' => null));

		foreach ($pages as $page) {

			if ($page->getPath() === 'homepage') {
				continue;
			}

			$menu->addChild($page->getTitle(), array(
			    'route' => 'maci_page',
			    'routeParameters' => array('path' => $page->getPath())
			));

		}

		$menu->addChild('Media', array('route' => 'maci_media'));

		$menu->addChild('Shop', array('route' => 'maci_product'));

		$menu->addChild('Blog', array('route' => 'maci_blog'));

		$menu->addChild('List', array('route' => 'maci_list'));

		$menu->addChild('Contacts', array('route' => 'maci_page_contacts'));

		return $menu;
	}

    public function createLeftMenu(Request $request)
	{
		$menu = $this->factory->createItem('root');

		$menu->addChild('Home', array('route' => 'maci_homepage'));

		$pages = $this->om->getRepository('MaciPageBundle:Page')->findBy(array('parent' => null));

		foreach ($pages as $page) {

			if ($page->getPath() === 'homepage') {
				continue;
			}

			$menu->addChild($page->getTitle(), array(
			    'route' => 'maci_page',
			    'routeParameters' => array('path' => $page->getPath())
			));

		}

		return $menu;
	}
}
