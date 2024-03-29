<?php

namespace Maci\PageBundle\Form\Order;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CheckoutShippingType extends AbstractType
{
	protected $orders;

	public function __construct($orders)
	{
		$this->orders = $orders;
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
		$choices = $this->orders->getShippingChoices($options['data']);
		if (!$choices) return;

		$builder
			->add('shipping', ChoiceType::class, [
				'label' => 'Shipping Method',
                'choices' => $choices,
	            'preferred_choices' => (is_string($str = $this->orders->getCartShippingCountry()) ? [$str] : []),
                'expanded' => true
            ])
			->add('set_shipping', SubmitType::class, [
				'label' => 'Set Shipping',
				'attr' => ['class' => 'btn btn-primary']
			])
		;
	}

	public function getName()
	{
		return 'order_checkout_shipping';
	}
}
