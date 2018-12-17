<?php

namespace Maci\PageBundle\Form\Media;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Album
 */
class AlbumType extends AbstractType
{
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'Maci\PageBundle\Entity\Media\Album',
			// 'cascade_validation' => true,
		));
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name')
			->add('subtitle',null,array('required'=>false))
			->add('description',null,array('required'=>false))
			->add('meta_title',null,array('required'=>false))
			->add('meta_description',null,array('required'=>false))
			->add('type', 'choice', array(
			    'choices' => $builder->getData()->getTypeArray()
			))
			->add('parent')
			->add('reset', 'reset')
			->add('send', 'submit')
		;
	}

	public function getName()
	{
		return 'media_album';
	}
}
