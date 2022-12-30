<?php

namespace Maci\PageBundle\Form\Contact;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Email;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;

/**
 * Contact
 */
class ContactType extends AbstractType
{
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'Maci\PageBundle\Entity\Contact\Contact',
			'env' => 'prod'
			// 'cascade_validation' => true,
		));
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name')
			->add('surname', null, array('required' => false))
			->add('email', EmailType::class, array(
				'constraints' => new Email(array(
					'message' => 'Insert your Email'
				))
			))
			->add('media', FileType::class, array('mapped' => false, 'required' => false))
			->add('message', TextareaType::class)
			->add('privacy', CheckboxType::class, [
				'mapped' => false,
				'constraints' => new IsTrue(array(
					'message' => 'Please accept the Terms and Conditions'
				))
			])
		;

		if($options['env'] === "prod")
			$builder->add('recaptcha', EWZRecaptchaType::class, array(
				'label_attr'  => array('class'=> 'sr-only'),
				'mapped'      => false,
				'constraints' => array(
					new RecaptchaTrue()
				)
			));

		$builder
			->add('cancel', ResetType::class)
			->add('send', SubmitType::class)
		;
	}

	public function getName()
	{
		return 'contact';
	}
}
