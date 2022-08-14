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

    public function createTagMenu(array $options)
	{
		$menu = $this->factory->createItem('root');

		$menu->setChildrenAttribute('class', 'nav flex-column');

		$tags = $this->om->getRepository('MaciPageBundle:Blog\Tag')
			->getlist($this->request->getLocale());

		foreach ($tags as $tag) {

			$menu->addChild($tag->getName(), [
			    'route' => 'maci_blog_tag',
			    'routeParameters' => ['path' => $tag->getPath()]
			]);

		}

		return $menu;
	}

    public function createAuthorMenu(array $options)
	{
		$menu = $this->factory->createItem('root');

		$menu->setChildrenAttribute('class', 'nav flex-column');

		$authors = $this->om->getRepository('MaciPageBundle:Blog\Author')
			->getlist($this->request->getLocale());

		foreach ($authors as $author) {

			$menu->addChild($author->getName(), [
			    'route' => 'maci_blog_author',
			    'routeParameters' => ['path' => $author->getPath()]
			]);

		}

		return $menu;
	}
}
