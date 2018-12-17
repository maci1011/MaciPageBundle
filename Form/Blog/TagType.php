<?php

namespace Maci\PageBundle\Form\Blog;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Tag
 */
class TagType extends AbstractType
{
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'Maci\PageBundle\Entity\Blog\Tag',
			// 'cascade_validation' => true,
		));
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name')
			->add('description', null, array('required' => false, 'attr' => array('class'=>'noeditor')))
			->add('favourite', 'choice', array(
				'choices'   => array(0 => 'No', 1 => 'Yes')
			))
			->add('locale')
			->add('reset', 'reset')
			->add('send', 'submit')
		;
	}

	public function getName()
	{
		return 'blog_tag';
	}
}
