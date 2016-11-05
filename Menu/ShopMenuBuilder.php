<?php

namespace Maci\PageBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\Common\Persistence\ObjectManager;

use Maci\TranslatorBundle\Controller\TranslatorController;

class ShopMenuBuilder
{
	private $factory;

	private $securityContext;

	private $user;

    private $om;

	private $translator;

	private $locales;

	public function __construct(FactoryInterface $factory, SecurityContext $securityContext, ObjectManager $om, TranslatorController $tc)
	{
	    $this->factory = $factory;
	    $this->securityContext = $securityContext;
	    $this->user = $securityContext->getToken()->getUser();
        $this->om = $om;
	    $this->translator = $tc;
	    $this->locales = $tc->getLocales();
	}

    public function createCustomerServiceMenu(Request $request)
	{
		$menu = $this->factory->createItem('root');

		$menu->setChildrenAttribute('class', 'nav');

		$menu->addChild($this->translator->getText('menu.shopping.customer_service', 'Customer Service'), array('route' => 'maci_page', 'routeParameters' => array('path' => 'customer-service')));

		$menu->addChild($this->translator->getText('menu.shopping.size', 'Size Guide'), array('route' => 'maci_page', 'routeParameters' => array('path' => 'size-guide')));

		$menu->addChild($this->translator->getText('menu.shopping.guide', 'Shopping Guide'), array('route' => 'maci_page', 'routeParameters' => array('path' => 'shopping-guide')));

		$menu->addChild($this->translator->getText('menu.shopping.shipping', 'Shipping'), array('route' => 'maci_page', 'routeParameters' => array('path' => 'shipping')));

		$menu->addChild($this->translator->getText('menu.shopping.payment', 'Payment'), array('route' => 'maci_page', 'routeParameters' => array('path' => 'payment')));

		$menu->addChild($this->translator->getText('menu.shopping.refunds', 'Returns And Refunds'), array('route' => 'maci_page', 'routeParameters' => array('path' => 'refunds')));

		return $menu;
	}

    public function createTermsMenu(Request $request)
	{
		$menu = $this->factory->createItem('root');

		$menu->setChildrenAttribute('class', 'nav');

		$menu->addChild($this->translator->getText('menu.terms.sale', 'Sale Terms'), array('route' => 'maci_page', 'routeParameters' => array('path' => 'sale-terms')));

		$menu->addChild($this->translator->getText('menu.terms.cookie', 'Cookie Policy'), array('route' => 'maci_page', 'routeParameters' => array('path' => 'cookie')));

		$menu->addChild($this->translator->getText('menu.terms.privacy', 'Privacy Policy'), array('route' => 'maci_page', 'routeParameters' => array('path' => 'privacy')));

		return $menu;
	}
}
