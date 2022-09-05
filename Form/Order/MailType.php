<?php

namespace Maci\PageBundle\Form\Order;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Maci\TranslatorBundle\Controller\TranslatorController;

class MailType extends AbstractType
{
	protected $translator;

	public function __construct(TranslatorController $translator)
	{
		$this->translator = $translator;
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'Maci\PageBundle\Entity\Order\Order',
			'cascade_validation' => true
		));
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('mail', EmailType::class, [
				'label' => $this->translator->getLabel('mail', 'Mail')
			])
			->add('use_this_email', SubmitType::class, [
				'label' => $this->translator->getLabel('use-this-mail', 'Use This Mail'),
				'attr' => ['class' => 'btn btn-primary']
			])
		;
	}

	public function getName()
	{
		return 'order_mail';
	}
}
