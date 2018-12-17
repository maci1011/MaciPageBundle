<?php

namespace Maci\PageBundle\Form\Media;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Media
 */
class MediaType extends AbstractType
{
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'Maci\PageBundle\Entity\Media\Media',
			// 'cascade_validation' => true,
		));
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
            ->add('name', null, array('required' => false))
            ->add('description', null, array('required' => false))
			->add('file', 'file', array('required' => false))
			->add('public', 'choice', array(
				'choices'   => array(0 => 'No', 1 => 'Yes')
			))
			->add('type', 'choice', array(
                'choices' => $builder->getData()->getTypeArray()
            ))
			->add('reset', 'reset')
			->add('send', 'submit')
		;
	}

	public function getName()
	{
		return 'media';
	}
}
