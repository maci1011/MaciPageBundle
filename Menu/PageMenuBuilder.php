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

		$pages = $this->om->getRepository('MaciPageBundle:Page')->findBy(array('parent' => null));

		$menu->setChildrenAttribute('class', 'nav navbar-nav');

		$menu->addChild('Home', array('route' => 'maci_homepage'));

		$menu->addChild('Gallery', array('route' => 'maci_media_gallery'));

		foreach ($pages as $page) {

			$menu->addChild($page->getTitle(), array(
			    'route' => 'maci_page',
			    'routeParameters' => array('path' => $page->getPath())
			));

		}

		$menu->addChild('Contacts', array('route' => 'maci_page_contacts'));

		return $menu;
	}

    public function createLeftMenu(Request $request)
	{
		$menu = $this->factory->createItem('root');

		$pages = $this->om->getRepository('MaciPageBundle:Page')->findBy(array('parent' => null));

		foreach ($pages as $page) {

			$menu->addChild($page->getTitle(), array(
			    'route' => 'maci_page',
			    'routeParameters' => array('path' => $page->getPath())
			));

		}

		return $menu;
	}
}
