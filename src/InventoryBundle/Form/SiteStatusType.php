<?php

namespace InventoryBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use InventoryBundle\Entity\Site;


class SiteStatusType extends AbstractType
{
	/**
	 *  {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('status', ChoiceType::class, array('choices' => Site::getStatus()));
	}
	
	/**
	 *  {@inheritdoc}
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array('data_class' => 'InventoryBundle\Entity\Site'));
	}
	
	/**
	 *  {@inheritdoc}
	 */
	public function getName()
	{
		return 'siteStatus';
	}
	
	
}