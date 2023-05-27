<?php

namespace Maci\PageBundle\Form\Shop;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Product
 */
class ProductType extends AbstractType
{
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'Maci\PageBundle\Entity\Shop\Product',
			// 'cascade_validation' => true,
		));
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name')
			->add('description',null,array('required'=>false))
			->add('composition',null,array('required'=>false))
			->add('code')
			->add('sale')
			->add('price')
			->add('shipment', 'choice', [
				'choices'   => array(0 => 'No', 1 => 'Yes')
			])
			->add('limited', 'choice', [
				'choices'   => array(0 => 'No', 1 => 'Yes')
			])
			->add('quantity')
			->add('status', 'choice', [
                'choices' => $builder->getData()->getStatusArray()
            ])
			->add('reset', 'reset')
			->add('send', 'submit')
		;
	}

	public function getName()
	{
		return 'product';
	}
}
