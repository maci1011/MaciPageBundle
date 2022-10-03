<?php

namespace Maci\PageBundle\Menu;

use Knp\Menu\FactoryInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Maci\TranslatorBundle\Controller\TranslatorController;

class OrderMenuBuilder
{
	private $factory;

	private $translator;

	public function __construct(FactoryInterface $factory, TranslatorController $tc)
	{
	    $this->factory = $factory;
	    $this->translator = $tc;
	}

    public function createShopAdminMenu(array $options)
	{
		$menu = $this->factory->createItem('root');

		$menu->setChildrenAttribute('class', 'nav');

		$menu->addChild($this->translator->getText('menu.admin.confirmed_orders', 'Confirmed Orders'), ['route' => 'maci_order_admin_confirmed']);

		return $menu;
	}
}
