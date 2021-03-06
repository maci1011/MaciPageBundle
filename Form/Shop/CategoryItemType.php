<?php

namespace Maci\PageBundle\Form\Shop;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * CategoryItem
 */
class CategoryItemType extends AbstractType
{
	public function setShopOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setShops(array(
			'data_class' => 'Maci\PageBundle\Entity\Shop\CategoryItem',
			// 'cascade_validation' => true,
		));
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('favourite', 'choice', array(
				'choices'   => array(0 => 'No', 1 => 'Yes')
			))
			->add('reset', 'reset')
			->add('send', 'submit')
		;
	}

	public function getName()
	{
		return 'category_item';
	}
}
