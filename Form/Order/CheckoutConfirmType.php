<?php

namespace Maci\PageBundle\Form\Order;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;

class CheckoutConfirmType extends AbstractType
{
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'recaptcha' => false
		));

		$resolver->setAllowedTypes('recaptcha', 'bool');
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
	    if($options['recaptcha'] === true) {
			$builder->add('recaptcha', EWZRecaptchaType::class, array(
		    	'label_attr'  => array('class'=> 'sr-only'),
		        'mapped'      => false,
				'constraints' => array(
				    new RecaptchaTrue()
				)
		    ));
		}

		$builder
			->add('confirm', SubmitType::class, [
				'label' => 'Confirm',
				'attr' => ['class' => 'btn btn-primary']
			])
		;
	}

	public function getName()
	{
		return 'cart_checkout_confirm';
	}
}
