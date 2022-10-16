<?php

namespace Maci\PageBundle\Form\Order;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Maci\PageBundle\Entity\Order\Payment;

class PaymentType extends AbstractType
{
	protected $orders;

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'Maci\PageBundle\Entity\Payment',
			'cascade_validation' => true
		));
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('number')
			->add('description')
			->add('status', ChoiceType::class, [
				'choices' => Payment::getStatusArray()
			])
			->add('clientemail')
			->add('clientid')
			->add('totalamount')
			->add('currencycode')
			->add('details', CollectionType::class, [
				'entry_type' => TextType::class
			])
			->add('cancel', ResetType::class)
			->add('send', SubmitType::class)
		;
	}

	public function getName()
	{
		return 'order_payment';
	}
}
