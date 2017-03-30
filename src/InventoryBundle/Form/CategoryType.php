<?php

namespace InventoryBundle\Form;

use InventoryBundle\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        	->add('name', TextType::class)
        	->add('displayOrder', TextType::class)
        	
        	->add('parent', EntityType::class, array(
        					'class'         => 'InventoryBundle:Category',
        					'choice_label'  => 'name',
        					'multiple'      => false,
        			'query_builder' => function(CategoryRepository $repository) {
        			return $repository->getParentOnly();
        			}
        			))
        	->add('save',      SubmitType::class)
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'InventoryBundle\Entity\Category'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'inventorybundle_category';
    }


}
