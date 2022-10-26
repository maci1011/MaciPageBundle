<?php

namespace Maci\PageBundle\Form\Mailer;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;

use Maci\UserBundle\Form\Type\AddressType;
use Maci\TranslatorBundle\Controller\TranslatorController;

/**
 * Subscribe
 */
class SubscribeType extends AbstractType
{
	protected $request;

	protected $translator;

	public function __construct(RequestStack $requestStack, TranslatorController $translator)
	{
		$this->request = $requestStack->getCurrentRequest();
		$this->translator = $translator;
	}

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
		$builder
			->add('name')
			->add('mail', EmailType::class, array(
				'constraints' => new Email(array(
					'message' => 'Insert your Email'
				))
			))
		;

		if(is_array($options['locales'])) {
			$builder->add('locale', ChoiceType::class, array(
				'label_attr'  => array('class'=> 'sr-only'),
				'choices' => $options['locales']
			));
		} else {
			$builder->add('locale', LocaleType::class);
		}

		if($options['env'] === "prod") {
			$builder->add('recaptcha', EWZRecaptchaType::class, array(
				'label_attr'  => array('class'=> 'sr-only'),
				'mapped'      => false,
				'constraints' => array(
					new RecaptchaTrue()
				)
			));
		}

		$builder
			->add('privacy', CheckboxType::class, array(
				'mapped' => false,
				'constraints' => new IsTrue(array(
					'message' => 'Please accept the Terms and Conditions'
				))
			))
			->add('subscribe', SubmitType::class)
		;
	}

	public function getName()
	{
		return 'subscribe';
	}
}
