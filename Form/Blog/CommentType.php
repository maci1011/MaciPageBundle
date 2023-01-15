<?php

namespace Maci\PageBundle\Form\Blog;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;

/**
 * CommentType
 */
class CommentType extends AbstractType
{
	protected $authorizationChecker;

	public function __construct(AuthorizationCheckerInterface $authorizationChecker)
	{
		$this->authorizationChecker = $authorizationChecker;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => 'Maci\PageBundle\Entity\Blog\Comment',
			'env' => 'prod'
		]);
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		if ($this->authorizationChecker->isGranted('ROLE_USER'))
		{
			$builder->add('content');
		}
		else
		{
			$builder
				->add('name')
				->add('email')
				->add('content')
				->add('notify', CheckboxType::class, [
					'required' => false
				])
				->add('newsletter', CheckboxType::class, [
					'mapped' => false,
					'required' => false
				])
			;
			if($options['env'] === "prod")
				$builder->add('recaptcha', EWZRecaptchaType::class, [
					'label_attr'  => ['class'=> 'sr-only'],
					'mapped'      => false,
					'constraints' => [new RecaptchaTrue()]
				]);
		}

		$builder->add('_parent', HiddenType::class, [
			'mapped' => false
		]);
		$builder->add('send_comment', SubmitType::class);
	}

	public function getName()
	{
		return 'blog_comment';
	}
}
