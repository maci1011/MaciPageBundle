<?php

namespace Maci\PageBundle\Form\Order;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CartEditItemType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
            ->add('quantity', IntegerType::class, array(
                'label_attr' => array('class' => 'sr-only'),
                'attr' => array('class' => 'edit-quantity-field')
            ))
            ->add('edit', SubmitType::class, array(
                'attr' => array('class' => 'btn-success')
            ))
		;
	}

	public function getName()
	{
		return 'cart_edit_item';
	}
}
