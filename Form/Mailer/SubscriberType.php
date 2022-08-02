<?php

namespace Maci\PageBundle\Form\Mailer;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;

use Maci\UserBundle\Form\Type\AddressType;

/**
 * Subscribe
 */
class SubscriberType extends AbstractType
{
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'Maci\PageBundle\Entity\Mailer\Subscriber',
			'env' => 'prod',
			'locales' => null
			// 'cascade_validation' => true
		));
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$years = [];
		for ($i=0; $i < 100; $i++) { 
			$years[$i] = 2021 - $i;
		}

		// $locales = [];
		// if(is_array($options['locales'])) {
		// 	$locales = $options['locales'];
		// } else {
		// 	$locales = ...
		// }

		$builder
			->add('name')
			->add('surname', null, ['required' => false])
			->add('birthdate', null, ['required' => false, 'years' => $years])
			->add('sex', ChoiceType::class, array(
				'choices' => ['Female' => 0, 'Male' => 1],
				'expanded' => true
			))
			->add('mail', EmailType::class, array(
				'constraints' => new Email(array(
					'message' => 'Insert your Email'
				))
			))
			->add('mobile', null, ['required' => false])
		;

		if(is_array($options['locales'])) {
			$builder->add('locale', ChoiceType::class, array(
				'choices' => $options['locales']
			));
		} else {
			$builder->add('locale', ChoiceType::class, array(
				'empty_data' => '',
				'choices' => \Maci\AdminBundle\Controller\AdminController::getLocales()
			));
		}

		$builder
			->add('notes', null, ['required' => false])
			->add('newsletter')
			->add('sms')
			->add('phone')
			->add('address', AddressType::class, [
				'required' => false,
				'embedded' => true
			])
		;

		$builder
			->add('save', SubmitType::class)
		;
	}

	public function getName()
	{
		return 'subscribe';
	}
}
