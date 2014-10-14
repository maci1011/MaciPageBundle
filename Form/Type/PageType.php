<?php

namespace Maci\PageBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Page
 */
class PageType extends AbstractType
{
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'Maci\PageBundle\Entity\Page',
			// 'cascade_validation' => true
		));
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('translations', 'a2lix_translations', array(
                'fields' => array(
                    'title' => array(),
                    'content' => array(
                        'required' => false
                    ),
                    'meta_title' => array(
                        'required' => false
                    ),
                    'meta_description' => array(
                        'required' => false
                    )
                )
            ))
			// ->add('status')
			->add('path')
			->add('album')
			->add('gallery')
			->add('slider')
			->add('map')
			->add('cancel', 'reset')
			->add('send', 'submit')
		;
	}

	public function getName()
	{
		return 'page';
	}
}
