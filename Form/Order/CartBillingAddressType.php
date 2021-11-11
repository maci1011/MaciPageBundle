<?php

namespace Maci\PageBundle\Form\Order;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CartBillingAddressType extends AbstractType
{
	protected $orders;

	protected $addresses;

	public function __construct($orders, $addresses)
	{
		$this->orders = $orders;
		$this->addresses = $addresses;
	}

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
			->add('billing_address', ChoiceType::class, array(
				'label_attr' => array('class'=> 'sr-only'), 
				'choices' => $this->addresses->getAddressChoices(),
				'mapped' => false
			))
			->add('save', SubmitType::class)
		;
	}

	public function getName()
	{
		return 'cart_billing_address';
	}
}
