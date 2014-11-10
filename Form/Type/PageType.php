<?php

namespace Maci\PageBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

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
                    'subtitle' => array(
                        'required' => false
                    ),
                    'header' => array(
                        'required' => false
                    ),
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
			->add('album', 'entity', array(
				'class' => 'MaciMediaBundle:Album',
				'query_builder' => function(EntityRepository $er) {
					return $er->createQueryBuilder('a')
						->where('a.type = :type')
						->setParameter(':type', 'page_album')
					;
				},
				'empty_value' => ''
			))
			->add('gallery', 'entity', array(
				'class' => 'MaciMediaBundle:Album',
				'query_builder' => function(EntityRepository $er) {
					return $er->createQueryBuilder('a')
						->where('a.type = :type')
						->setParameter(':type', 'page_gallery')
					;
				},
				'empty_value' => ''
			))
			->add('slider', 'entity', array(
				'class' => 'MaciMediaBundle:Album',
				'query_builder' => function(EntityRepository $er) {
					return $er->createQueryBuilder('a')
						->where('a.type = :type')
						->setParameter(':type', 'page_slider')
					;
				},
				'empty_value' => ''
			))
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
