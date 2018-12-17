<?php

namespace Maci\PageBundle\Form\Page;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Search
 */
class SearchType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
                  ->add('section', ChoiceType::class, array(
                  	'choices' => array(
                  		'all' => 'All',
                              'blog' => 'Blog',
                              'media' => 'Media',
                              // 'shop' => 'Shop',
                              'page' => 'Pages'
                  	)
                  ))
                  ->add('query')
                  ->add('submit', SubmitType::class)
		;
	}

	public function getName()
	{
		return 'search';
	}
}
