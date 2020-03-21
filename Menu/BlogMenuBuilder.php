<?php

namespace Maci\PageBundle\Menu;

use Knp\Menu\FactoryInterface;
use Doctrine\Common\Persistence\ObjectManager;

class BlogMenuBuilder
{
	private $factory;

    private $om;

	public function __construct(FactoryInterface $factory, ObjectManager $objectManager)
	{
	    $this->factory = $factory;
        $this->om = $objectManager;
	}

    public function createLeftMenu(array $options)
	{
		$menu = $this->factory->createItem('root');

		$menu->setChildrenAttribute('class', 'nav flex-column');

		$tags = $this->om->getRepository('MaciPageBundle:Blog\Tag')->findAll();

		foreach ($tags as $tag) {

			$menu->addChild($tag->getName(), array(
			    'route' => 'maci_blog_tag',
			    'routeParameters' => array('id' => $tag->getId())
			));

		}

		return $menu;
	}
}
