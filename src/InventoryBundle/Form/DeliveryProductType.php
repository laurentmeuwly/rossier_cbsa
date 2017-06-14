<?php

namespace InventoryBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use InventoryBundle\Entity\Product;

class DeliveryProductType extends AbstractType
{
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
           	->add('quantity', NumberType::class ,array('attr' => array('class' => 'col-sm-1')))
        	->add('unit', EntityType::class ,
                        		array('class' => 'InventoryBundle\Entity\Unit',
                        				'attr' => array('class' => 'col-sm-1'),
                        				'placeholder' => ''
                        		))
           	->add('deliveryCostPrice', NumberType::class ,array('attr' => array('class' => 'col-sm-1')))
                        
		;
           	/*$formModifier = function (FormInterface $form, Product $product = null) {
           		$unit = null === $product ? '' : $product->getUnit();
           	
           		$form->add('unit', TextType::class, array(
           				'data'     => $unit,
           				'disabled'=> 'disabled',
           		));
           	};
           	
           	$builder->addEventListener(
           			FormEvents::PRE_SET_DATA,
           			function (FormEvent $event) use ($formModifier) {
           			$data = $event->getData();
      
           			if ($data) {
           				$formModifier($event->getForm(), $data->getProduct());
           			}
           		});
           	
           		$builder->get('product')->addEventListener(
           				FormEvents::POST_SUBMIT,
           				function (FormEvent $event) use ($formModifier) {
           					// It's important here to fetch $event->getForm()->getData(), as
           					// $event->getData() will get you the client data (that is, the ID)
           					$product = $event->getForm()->getData();
           		
           					// since we've added the listener to the child, we'll have to pass on
           					// the parent to the callback functions!
           					$formModifier($event->getForm()->getParent(), $product);
           				}
           				);
           				*/
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