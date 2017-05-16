<?php

namespace InventoryBundle\Controller;

use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as EasyAdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use InventoryBundle\Repository\CategoryRepository;
use InventoryBundle\Entity\Product;
use InventoryBundle\Entity\Category;

class AdminController extends EasyAdminController
{
	/**
	 * @Route("/", name="easyadmin")
	 * @Route("/", name="admin")
	 */
	public function indexAction(Request $request)
    {
    	return parent::indexAction($request);
    }
    
    public function createNewProductEntity()
    {
    	return new Product();
    }
    
    public function createCategoryEntityFormBuilder($entity, $view)
    {
    	$formBuilder = parent::createEntityFormBuilder($entity, $view);
    
    	// get the list of categories and add the form field for them
    	// OK for new Category
    	// if editing an existing category, please don't assign itself as parent to avoid loop
    	$formBuilder->add('parent', EntityType::class, array(
    			'class'         => 'InventoryBundle:Category',
    			'choice_label'  => 'name',
    			'multiple'      => false,
				'required'		=> false,
    			'placeholder' 	=> 'Aucun(e)',
    			'query_builder' => function(CategoryRepository $repository) {
    			return $repository->getParentOnly($this->request->query->get('id'));
    			}
    			));
    
    	return $formBuilder;
    }
    
    /**
     * 
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function report01Action()
    {
    	return $this->forward('InventoryBundle:Listing:printSiteResume', array('id' => $this->request->query->get('id'), 'report' => 'perDeliveries'));
    }
    
    /**
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function report02Action()
    {
    	return $this->forward('InventoryBundle:Listing:printSiteResume', array('id' => $this->request->query->get('id'), 'report' => 'perProducts'));
    }
    
    /**
     * 
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function restockAction()
    {
    	$form = $this->createFormBuilder()->add('quantity')->getForm();
    	
    	return $this->render('InventoryBundle:Product:changeQty.html.twig', [
    			'form' => $form->createView()
    	]);
    	
    	/*$id = $this->request->query->get('id');
    	$entity = $this->em->getRepository('InventoryBundle:Product')->find($id);
    	$entity->setStock(10 + $entity->getStock());	// 10 = constante pour validation du procédé!
    	$this->em->flush();
    	
    	// redirect to the 'list' view of the given entity
    	return $this->redirectToRoute('easyadmin', array(
    			'action' => 'list',
    			'entity' => $this->request->query->get('entity'),
    	));*/
    }
    
    /**
     * 
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function updateCostAction()
    {
    	$id = $this->request->query->get('id');
    	$entity = $this->em->getRepository('InventoryBundle:Product')->find($id);
    	$entity->setCostPrice('9.99');	// 9.99 = constante pour validation du procédé!
    	$this->em->flush();
    	 
    	// redirect to the 'list' view of the given entity
    	return $this->redirectToRoute('easyadmin', $this->request->query->all());
    }
    
    /**
     * @Route("/impressum", name="impressum")
     */
    
    public function impressumAction()
    {
    	return $this->render('::impressum.html.twig');
    }
}