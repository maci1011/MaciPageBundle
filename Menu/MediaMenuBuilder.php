<?php

namespace Maci\PageBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Maci\TranslatorBundle\Controller\TranslatorController;

class MediaMenuBuilder
{
	private $factory;

	private $authorizationChecker;

    private $om;

	private $translator;

	private $locales;


	public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $authorizationChecker, ObjectManager $objectManager, TranslatorController $tc)
	{
	    $this->factory = $factory;
	    $this->authorizationChecker = $authorizationChecker;
        $this->om = $objectManager;
	    $this->translator = $tc;
	    $this->locales = $tc->getLocales();
	}

    public function createAlbumMenu(array $options)
	{
		$menu = $this->factory->createItem('root');

		$menu->setChildrenAttribute('class', 'nav');

		if (!array_key_exists('album', $options)) {
			return $menu;
		}

		$album = $options['album'];

		if ($album->getParent()) {
			$album = $album->getParent();
		}

		$menu->addChild($album->getLabel(), array(
		    'route' => 'maci_media_album',
		    'routeParameters' => array('id' => $album->getId())
		));

		$this->addChildren($menu[$album->getLabel()], $album);

		return $menu;
	}

    public function createGalleryMenu(array $options)
	{
		$menu = $this->factory->createItem('root');

		$menu->setChildrenAttribute('class', 'nav');

        if (true === $this->authorizationChecker->isGranted('ROLE_ADMIN')) {

			$menu->addChild($this->translator->getText('menu.media', 'Media'), array('route' => 'maci_media'));

        }

		$this->addGalleryChild($menu, $this->translator->getText('menu.media.gallery', 'Gallery'), 'gallery');

        $this->addTagChild($menu, $this->translator->getText('menu.media.tags', 'Tags'), 'tag');

		$this->addGalleryChild($menu, $this->translator->getText('menu.media.products', 'Products'), 'products');

        $this->addTagChild($menu, $this->translator->getText('menu.media.brands', 'Brands'), 'brand', 'maci_media_brand');

        if (true === $this->authorizationChecker->isGranted('ROLE_ADMIN')) {

			$this->addGalleryChild($menu, $this->translator->getText('menu.media.album', 'Album'), 'album');

        }

		return $menu;
	}

    public function addGalleryChild($menu, $child, $type)
	{
		$albums = $this->om->getRepository('MaciPageBundle:Media\Album')->findBy(array(
			'type' => $type,
			'parent' => null
		));
		if (count($albums)) {
			$menu->addChild($child, array(
			    'route' => 'maci_media_gallery',
			    'routeParameters' => array('type' => $type)
			));
			$menu[$child]->setChildrenAttribute('class', 'nav');
			foreach ($albums as $album) {
				$menu[$child]->addChild($album->getLabel(), array(
				    'route' => 'maci_media_album',
				    'routeParameters' => array('id' => $album->getId())
				));
				$this->addChildren($menu[$child][$album->getLabel()], $album);
			}
		}
	}

    public function addTagChild($menu, $child, $type, $route = false)
	{
		$tags = $this->om->getRepository('MaciPageBundle:Media\Tag')->findBy(array(
			'type' => $type
		));
		if (count($tags)) {
			if ( $type === 'brand' ) {
	        	$menu->addChild($child, array(
				    'route' => 'maci_media_brands'
				));
			} else {
	        	$menu->addChild($child, array(
				    'route' => 'maci_media_tags',
				    'routeParameters' => array('type' => $type)
				));
	        }
			$menu[$child]->setChildrenAttribute('class', 'nav');
			foreach ($tags as $tag) {
				$menu[$child]->addChild($tag->getName(), array(
				    'route' => ( $route ? $route : 'maci_media_tag' ),
				    'routeParameters' => array('id' => $tag->getId())
				));
			}
		}

	}

    public function addChildren($menu, $item)
	{
		if (count($item->getChildren())) {
			$menu->setChildrenAttribute('class', 'nav');
			foreach ($item->getChildren() as $child) {
				$menu->addChild($child->getLabel(), array(
				    'route' => 'maci_media_album',
				    'routeParameters' => array('id' => $child->getId())
				));
				$this->addChildren($menu[$child->getLabel()], $child);
			}
		}
	}
}
