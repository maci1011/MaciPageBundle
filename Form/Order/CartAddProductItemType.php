<?php

namespace Maci\PageBundle\Form\Order;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
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
			if (-1 < $index)
			{
				$variants = $product->getVariants();
				$variant = $variants[$index];
				$builder->add('variant', HiddenType::class, [
						'data' => $variant['name']
				]);
				$builder->add('quantity', ChoiceType::class, [
					'choices' => [1,2,3],
					'label' => $this->translator->getLabel('quantity', 'Quantity'),
					'attr' => ['class' => 'set-quantity-field']
				]);
			}
			else if($product->getShipment())
			{
				$builder->add('quantity', ChoiceType::class, [
					'choices' => $this->getIntChoices($product->getQuantity()),
					'label' => ($this->translator->getLabel('quantity', 'Quantity')),
					'attr' => ['class' => 'edit-quantity-field']
				]);
			}
			else
			{
				$builder->add('quantity', HiddenType::class, [
					'data' => 1
				]);
			}
		}
		$builder->add('add_to_cart', SubmitType::class, [
			'label' => $this->translator->getLabel('product.add_to_cart', 'Add To Cart'),
			'attr' => array('class' => 'btn-primary btn')
		]);
	}

	public function getIntChoices($max)
	{
		$list = [];
		
		for ($i=1; $i <= $max; $i++)
			$list[$i] = $i;
		
		return $list;
	}

	public function getName()
	{
		return 'cart_add_product_item';
	}
}
