<?php

namespace Maci\PageBundle\Menu;

use Knp\Menu\FactoryInterface;
use Maci\TranslatorBundle\Controller\TranslatorController;

class MailerMenuBuilder
{
	private $factory;

	private $translator;

	private $locales;

	public function __construct(FactoryInterface $factory, TranslatorController $tc)
	{
	    $this->factory = $factory;
	    $this->translator = $tc;
	    $this->locales = $tc->getLocales();
	}

    public function createLeftMenu(array $options)
	{
		$menu = $this->factory->createItem('root');

		$menu->setChildrenAttribute('class', 'nav');

		$menu->addChild($this->translator->getText('menu.admin.mailer.templates', 'Templates'), array('route' => 'maci_mailer_templates'));

		return $menu;
	}
}
