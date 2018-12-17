<?php

namespace Maci\PageBundle\Form\Shop;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * MediaItem
 */
class MediaItemType extends AbstractType
{
	public function setShopOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setShops(array(
			'data_class' => 'Maci\PageBundle\Entity\Shop\MediaItem',
			// 'cascade_validation' => true,
		));
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('favourite', 'choice', array(
				'choices'   => array(0 => 'No', 1 => 'Yes')
			))
			->add('reset', 'reset')
			->add('send', 'submit')
		;
	}

	public function getName()
	{
		return 'product_media_item';
	}
}
