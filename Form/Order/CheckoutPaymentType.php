<?php

namespace Maci\PageBundle\Form\Order;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CheckoutPaymentType extends AbstractType
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
		$builder
			->add('payment', ChoiceType::class, [
                'choices' => $this->orders->getPaymentChoices(),
                'expanded' => true
            ])
			->add('set_payment', SubmitType::class, [
				'label' => 'Set Payment',
				'attr' => ['class' => 'btn btn-primary']
			])
		;
	}

	public function getChoices($array)
	{
		$result = array();
		foreach ($array as $key => $value) {
			$result[$key] = ( $value['label'] . ( $value['cost'] ? ( ' ( ' . number_format($value['cost'], 2, '.', ',') . ' EUR )' ) : null ) );
		}
		return $result;
	}

	public function getName()
	{
		return 'order_checkout_payment';
	}
}
