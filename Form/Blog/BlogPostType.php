<?php

namespace Maci\PageBundle\Form\Blog;

use Maci\PageBundle\Entity\Blog\Post;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


/**
 * Post
 */
class BlogPostType extends AbstractType
{
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => Post::class,
			// 'cascade_validation' => true,
		));
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('title')
			->add('subtitle', null, array('required' => false))
			->add('header', null, array('required' => false))
			->add('content', null, array('required' => false))
			->add('meta_title', null, array('required' => false))
			->add('meta_description', null, array('required' => false))
			->add('locale')
			->add('reset', ResetType::class)
			->add('save', SubmitType::class)
		;
	}

	public function getName()
	{
		return 'blog_post';
	}
}
