<?php

namespace Maci\PageBundle\Form\Page;

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
			'data_class' => 'Maci\PageBundle\Entity\Page\Page',
			// 'cascade_validation' => true
		));
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('title')
			->add('subtitle', null, array('required' => false))
			->add('description', null, array('required' => false))
			->add('header', null, array('required' => false))
			->add('content', null, array('required' => false))
			->add('text', null, array('required' => false))
			->add('footer', null, array('required' => false))
			->add('meta_title', null, array('required' => false))
			->add('meta_description', null, array('required' => false))
			// ->add('status')
			->add('locale')
			->add('path')
			->add('template', 'choice', array(
                'choices' => $builder->getData()->getTemplateArray()
            ))
			->add('album', 'entity', array(
				'class' => 'MaciPageBundle:Album',
				'query_builder' => function(EntityRepository $er) {
					return $er->createQueryBuilder('a')
						->where('a.type = :type1')
						->orWhere('a.type = :type2')
						->setParameter(':type1', 'gallery')
						->setParameter(':type2', 'page_album')
					;
				},
				'empty_value' => '',
				'required' => false
			))
			->add('gallery', 'entity', array(
				'class' => 'MaciPageBundle:Album',
				'query_builder' => function(EntityRepository $er) {
					return $er->createQueryBuilder('a')
						->where('a.type = :type1')
						->orWhere('a.type = :type2')
						->setParameter(':type1', 'gallery')
						->setParameter(':type2', 'page_album')
					;
				},
				'empty_value' => '',
				'required' => false
			))
			->add('slider', 'entity', array(
				'class' => 'MaciPageBundle:Album',
				'query_builder' => function(EntityRepository $er) {
					return $er->createQueryBuilder('a')
						->where('a.type = :type')
						->setParameter(':type', 'page_slider')
					;
				},
				'empty_value' => '',
				'required' => false
			))
			->add('slides', 'entity', array(
				'class' => 'MaciPageBundle:Album',
				'query_builder' => function(EntityRepository $er) {
					return $er->createQueryBuilder('a')
						->where('a.type = :type')
						->setParameter(':type', 'page_slides')
					;
				},
				'empty_value' => '',
				'required' => false
			))
			->add('category', 'entity', array(
				'class' => 'MaciPageBundle:Shop\Category',
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
