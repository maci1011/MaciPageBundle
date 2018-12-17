<?php

namespace Maci\PageBundle\Form\Media;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;


/**
 * Item
 */
class ItemType extends AbstractType
{
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'Maci\PageBundle\Entity\Media\Item',
			// 'cascade_validation' => true,
		));
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name',null,array('required'=>false))
			->add('description',null,array('required'=>false))
			->add('favourite', 'choice', array(
				'choices'   => array(0 => 'No', 1 => 'Yes')
			))
			->add('link')
			->add('video', null, array('attr' => array('class'=>'noeditor')))
			->add('style', 'choice', array(
                'choices' => $builder->getData()->getStyleArray()
            ))
			->add('brand', 'entity', array(
				'class' => 'MaciPageBundle:Tag',
				'query_builder' => function(EntityRepository $er) {
					return $er->createQueryBuilder('a')
						->where('a.type = :type')
						->setParameter(':type', 'brand')
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
			->add('reset', 'reset')
			->add('send', 'submit')
		;
	}

	public function getName()
	{
		return 'media_item';
	}
}
