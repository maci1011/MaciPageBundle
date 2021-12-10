<?php

namespace Maci\PageBundle\Form\Order;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Maci\TranslatorBundle\Controller\TranslatorController;


class CartAddProductItemType extends AbstractType
{
	protected $translator;

	protected $product;

	public function __construct(TranslatorController $translator)
	{
		$this->translator = $translator;
		$this->product = false;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			// 'data_class' => 'Maci\PageBundle\Entity\Item',
			'cascade_validation' => true,
			'product' => false,
			'variant' => false
		));
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$product = $options['product'];

		$builder->add('product', HiddenType::class, array(
				'data' => ($product ? $product->getId() : null)
		));
		if ($product)
		{
			$index = $product->findVariant($options['variant']);
			if ($index)
			{
				$variants = $product->getVariants();
				$variant = $variants[$index];
				$builder->add('variant', HiddenType::class, array(
						'data' => $variant['size']
				));
				$builder->add('quantity', IntegerType::class, array(
					'data' => 1,
					'label' => ($this->translator->getLabel('cart_add_product_item.label', 'Select Quantity')),
					'attr' => array_merge(array('class' => 'edit-quantity-field', 'min' => 1),(
						($product->getShipment()) ? array('max' => $variant['quantity']) : array()
					))
				));
			}
			else if($product->getShipment())
			{
				$builder->add('quantity', IntegerType::class, array(
					'data' => 1,
					'label' => ($this->translator->getLabel('cart_add_product_item.label', 'Select Quantity')),
					'attr' => array_merge(array('class' => 'edit-quantity-field', 'min' => 1),(
						($product->getLimited()) ? array('max' => $product->getQuantity()) : array()
					))
				));
			}
			else
			{
				$builder->add('quantity', HiddenType::class, array(
					'data' => 1
				));
			}
		}
		$builder->add('add_to_cart', SubmitType::class, array(
			'label' => $this->translator->getLabel('product.add_to_cart', 'Add To Cart'),
			'attr' => array('class' => 'btn-primary btn')
		));
	}

	public function getName()
	{
		return 'cart_add_product_item';
	}
}
