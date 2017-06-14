<?php

namespace InventoryBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use InventoryBundle\Repository\SiteRepository;
use InventoryBundle\Form\DeliveryProductType;


class DeliveryType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	// if $site setted, hide it
    	
        $builder
          ->add('deliveryDate')
          ->add('site', EntityType::class, array(
          		'label' => false,
          		'class' => 'InventoryBundle:Site',
          		'query_builder' => function (SiteRepository $er) {
          		$qb = $er->createQueryBuilder('c');
          		return $qb->where($qb->expr()->eq('c.id', 4));
          		},
          		'attr' => array(
          				'class' => 'hidden'
          		)
          		))
          ->add('original_site', TextType::class, array('mapped' => false))
          ->add('deliveryProducts', DeliveryProductType::class)
          ->add('save',      SubmitType::class)
        ;
          

    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'InventoryBundle\Entity\Delivery'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'inventorybundle_delivery';
    }


}
