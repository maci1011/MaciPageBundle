<?php

namespace Maci\PageBundle\Menu;

use Knp\Menu\FactoryInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;

class BlogMenuBuilder
{
	private $factory;

    private $om;

	private $request;

	public function __construct(FactoryInterface $factory, ObjectManager $objectManager, RequestStack $requestStack)
	{
	    $this->factory = $factory;
        $this->om = $objectManager;
        $this->request = $requestStack->getCurrentRequest();
	}

    public function createLeftMenu(array $options)
	{
		$menu = $this->factory->createItem('root');

		$menu->setChildrenAttribute('class', 'navbar-nav flex-column');

		$tags = $this->om->getRepository('MaciPageBundle:Blog\Tag')
			->getlist($this->request->getLocale());

		foreach ($tags as $tag) {

			$menu->addChild($tag->getName(), array(
			    'route' => 'maci_blog_tag',
			    'routeParameters' => array('path' => $tag->getPath())
			));

		}

		return $menu;
	}
}
