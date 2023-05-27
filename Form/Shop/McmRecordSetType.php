<?php

namespace Maci\PageBundle\Form\Shop;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


class McmRecordSetType extends AbstractType
{
	protected $authorizationChecker;

	public function __construct(AuthorizationCheckerInterface $authorizationChecker)
	{
		$this->authorizationChecker = $authorizationChecker;
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('label');
		$builder->add('description');
		if ($this->authorizationChecker->isGranted('ROLE_ADMIN_MANAGER'))
			$builder->add('type', ChoiceType::class, [
                'choices' => $builder->getData()->getTypeArray()
            ]);
		$builder->add('save', SubmitType::class, [
			'label'=>'Save',
			'attr'=> ['class'=>'btn btn-success']
		]);
		$builder->add('save_and_add', SubmitType::class, [
			'label'=>'Save & Add a New Item',
			'attr'=> ['class'=>'btn btn-primary']
		]);
		$builder->add('save_and_list', SubmitType::class, [
			'label'=>'Save & Return to List',
			'attr'=> ['class'=>'btn btn-primary']
		]);
		$builder->add('reset', ResetType::class, [
			'label'=>'Reset Form'
		]);
	}

	public function getName()
	{
		return 'mcm_record_set_type';
	}
}
