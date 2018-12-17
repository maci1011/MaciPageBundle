<?php

namespace Maci\PageBundle\Form\Shop;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Category
 */
class CategoryType extends AbstractType
{
	public function setShopOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setShops(array(
			'data_class' => 'Maci\PageBundle\Entity\Shop\Category',
			// 'cascade_validation' => true,
		));
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name')
			->add('description',null,array('required'=>false))
			->add('locale')
			->add('reset', 'reset')
			->add('send', 'submit')
		;
	}

	public function getName()
	{
		return 'category';
	}
}
