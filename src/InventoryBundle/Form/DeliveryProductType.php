<?php

namespace InventoryBundle\Form;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use InventoryBundle\Entity\Product;



class DeliveryProductType extends AbstractType
{	
	protected $em;
	
	function __construct(EntityManager $em)
	{
		$this->em = $em;
	}
	
	/**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('product', 
                    EntityType::class ,
                    array('class' => 'InventoryBundle\Entity\Product',
                        'attr' => array('class' => 'col-sm-2'),
                    	'placeholder' => ''
                    ))
           	->add('unit', EntityType::class ,
                        		array('class' => 'InventoryBundle\Entity\Unit',
                        				'attr' => array('class' => 'col-sm-1'),
                        				'placeholder' => ''
                        		))
            ->add('deliveryCostPrice', NumberType::class ,array('attr' => array('class' => 'col-sm-1')))
           	->add('quantity', NumberType::class ,array('attr' => array('class' => 'col-sm-1')))
		;
		
           	// Add listeners
           	//$builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
           	$builder->addEventListener(FormEvents::POST_SUBMIT, array($this, 'onPostSubmit'));
           	

	}
	
	function onPostSubmit(FormEvent $event) {
		$form = $event->getForm();
		$data = $event->getData();
		
		$product = $this->em->getRepository('InventoryBundle:Product')->find($data->getProduct());
		
		
		if($data->getUnit()=="") {
			$data->setUnit($product->getUnit());
		}
		if($data->getDeliveryCostPrice()=="") {
			$data->setDeliveryCostPrice($product->getCostPrice());
		}

	}
	
	/**
	 * @param OptionsResolver $resolver
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'InventoryBundle\Entity\DeliveryProduct',
				array('attr' => array('class' => 'col-sm-6'))
		));
	}
}