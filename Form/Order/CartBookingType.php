<?php

namespace Maci\PageBundle\Form\Order;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CartBookingType extends AbstractType
{
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'Maci\PageBundle\Entity\Order\Order',
			'cascade_validation' => true
		));
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('spedition', HiddenType::class, array(
				'data' => 'none'
            ))
			->add('payment', HiddenType::class, array(
				'data' => 'none'
            ))
			->add('checkout', HiddenType::class, array(
				'data' => 'booking'
            ))
			->add('cart_booking', SubmitType::class)
		;
	}

	public function getName()
	{
		return 'cart_booking';
	}
}
