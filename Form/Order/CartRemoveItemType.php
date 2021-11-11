<?php

namespace Maci\PageBundle\Form\Order;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CartRemoveItemType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
            ->add('remove', SubmitType::class, array(
                'attr' => array('class' => 'btn-danger')
            ))
		;
	}

	public function getName()
	{
		return 'cart_remove_item';
	}
}
