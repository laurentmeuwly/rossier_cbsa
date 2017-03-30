<?php

namespace InventoryBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SiteStatusType extends AbstractType
{
	const STATUS_RUNNING = 1;
	const STATUS_TERMINATED = 2;
	
	/** @var array user friendly named status */
	public static $siteStatus = array(
			Site::STATUS_RUNNING => "En cours",
			Site::STATUS_TERMINATED => "Terminé",
	);
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults( array(
				'choices' => array(
						'1' => 'En cours',
						'2' => 'Terminé',
				)
		));
	}
	
	public function getParent()
	{
		return ChoiceType::class;
	}
	
	/**
	 * @return array<string>
	 */
	public static function getAvailableStatus()
	{
		return [
				self::STATUS_RUNNING,
				self::STATUS_TERMINATED,
		];
	}
}