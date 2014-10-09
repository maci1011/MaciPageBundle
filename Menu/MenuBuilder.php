<?php

namespace Maci\PageBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;

class MenuBuilder
{
	private $factory;

    private $om;

	public function __construct(FactoryInterface $factory, ObjectManager $om)
	{
	    $this->factory = $factory;
        $this->om = $om;
	}

    public function createMainMenu(Request $request)
	{
		$menu = $this->factory->createItem('root');

		$pages = $this->om->getRepository('MaciPageBundle:Page')->findBy(array('parent' => null));

		$menu->setChildrenAttribute('class', 'nav navbar-nav');

		$menu->addChild('Home', array('route' => 'maci_homepage'));

		foreach ($pages as $page) {

			$menu->addChild($page->getTitle(), array(
			    'route' => 'maci_page',
			    'routeParameters' => array('path' => $page->getPath())
			));

		}

		$menu->addChild('Contacts', array('route' => 'maci_page_contacts'));

		return $menu;
	}
}
