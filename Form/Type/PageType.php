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
                    'description' => array(
                        'required' => false,
                        'attr' => array('class'=>'noeditor')
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
			->add('template', 'choice', array(
                'choices' => $builder->getData()->getTemplateArray()
            ))
			->add('album', 'entity', array(
				'class' => 'MaciMediaBundle:Album',
				'query_builder' => function(EntityRepository $er) {
					return $er->createQueryBuilder('a')
						->where('a.type = :type')
						->setParameter(':type', 'page_album')
					;
				},
				'empty_value' => '',
				'required' => false
			))
			->add('gallery', 'entity', array(
				'class' => 'MaciMediaBundle:Album',
				'query_builder' => function(EntityRepository $er) {
					return $er->createQueryBuilder('a')
						->where('a.type = :type')
						->setParameter(':type', 'page_gallery')
					;
				},
				'empty_value' => '',
				'required' => false
			))
			->add('slider', 'entity', array(
				'class' => 'MaciMediaBundle:Album',
				'query_builder' => function(EntityRepository $er) {
					return $er->createQueryBuilder('a')
						->where('a.type = :type')
						->setParameter(':type', 'page_slider')
					;
				},
				'empty_value' => '',
				'required' => false
			))
			->add('category', 'entity', array(
				'class' => 'MaciProductBundle:Category',
				'empty_value' => '',
				'required' => false
			))
			->add('tag', 'entity', array(
				'class' => 'MaciBlogBundle:Tag',
				'empty_value' => '',
				'required' => false
			))
			->add('parent', 'entity', array(
				'class' => 'MaciPageBundle:Page',
				'empty_value' => '',
				'required' => false
			))
			->add('map', null, array('attr'=>array('class'=>'noeditor')))
			->add('cancel', 'reset')
			->add('send', 'submit')
		;
	}

	public function getName()
	{
		return 'page';
	}
}
