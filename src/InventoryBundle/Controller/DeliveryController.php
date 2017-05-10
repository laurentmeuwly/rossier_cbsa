<?php

namespace InventoryBundle\Controller;

use InventoryBundle\Entity\Delivery;
use InventoryBundle\Entity\DeliveryProduct;
use InventoryBundle\Entity\Site;
use InventoryBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


/**
 * Delivery controller.
 *
 */
class DeliveryController extends Controller
{
    /**
     * Lists all delivery entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $deliveries = $em->getRepository('InventoryBundle:Delivery')->findAll();

        return $this->render('InventoryBundle:Delivery:index.html.twig', array(
            'deliveries' => $deliveries,
        ));
    }

    /**
     * Creates a new delivery entity.
     *
     */
    public function newAction(Request $request)
    {
    	$delivery = new Delivery();
    	
    	$data = $request->query->all();
    	
    	if(isset($data['site'])) {
    		$site = $this->getDoctrine()->getManager()->getRepository('InventoryBundle:Site')->find($data['site']);
    		$delivery->setSite($site);
    	}
    	if(isset($data['docType'])) {
    		$delivery->setDocType($data['docType']);
    	}
        
        $form = $this->createForm('InventoryBundle\Form\DeliveryType', $delivery);
        $form->handleRequest($request);

  
        if ($form->isSubmitted() && $form->isValid()) {
        	
            $em = $this->getDoctrine()->getManager();
            $em->persist($delivery);
            $em->flush($delivery);

            return $this->redirectToRoute('delivery_show', array('id' => $delivery->getId()));
        }

        
        
        return $this->render('InventoryBundle:Delivery:new.html.twig', array(
            'delivery' => $delivery,
        	'site' => $data['site'],
            'form' => $form->createView(),
        ));
    }
    
    public function new2Action(Request $request)
    {
    	$delivery = new Delivery();
    
    	$form = $this->createForm('InventoryBundle\Form\DeliveryType', $delivery);
    	$form->handleRequest($request);
    
    
    	if ($form->isSubmitted() && $form->isValid()) {
    		$em = $this->getDoctrine()->getManager();
    		$em->persist($delivery);
    		$em->flush($delivery);
    
    		return $this->redirectToRoute('delivery_show', array('id' => $delivery->getId()));
    	}
    
    
    
    	return $this->render('InventoryBundle:Delivery:new.html.twig', array(
    			'delivery' => $delivery,
    			'site' => 4,
    			'form' => $form->createView(),
    	));
    	
    }
    
    public function createAction(Site $site, $docType='SORTIE')
    {
    	$delivery = new Delivery();
    	
    	if(isset($site)) {
    		$delivery->setSite($site);
    	}
    	$delivery->setDocType($docType);
    	
    	$em = $this->getDoctrine()->getManager();
    	$em->persist($delivery);
    	$em->flush($delivery);
    	
    	return $this->redirectToRoute('delivery_show', array('id' => $delivery->getId()));
    }

    /**
     * Finds and displays a delivery entity.
     *
     */
    public function showAction(Delivery $delivery, Product $product=NULL, $ean=NULL, $setQty=false)
    {
        $deleteForm = $this->createDeleteForm($delivery);

        return $this->render('InventoryBundle:Delivery:show.html.twig', array(
            'delivery' => $delivery,
        	'product' => $product,
        	'ean' => $ean,
        	'setQty' => $setQty,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing delivery entity.
     *
     */
    public function editAction(Request $request, Delivery $delivery)
    {	
        $deleteForm = $this->createDeleteForm($delivery);
        $editForm = $this->createForm('InventoryBundle\Form\DeliveryType', $delivery);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('delivery_edit', array('id' => $delivery->getId()));
        }

        return $this->render('InventoryBundle:Delivery:edit.html.twig', array(
            'delivery' => $delivery,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a delivery entity.
     *
     */
    public function deleteAction(Request $request, Delivery $delivery)
    {
        $form = $this->createDeleteForm($delivery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($delivery);
            $em->flush($delivery);
        }

        return $this->redirectToRoute('delivery_index');
    }
    
    public function customDeleteAction(Delivery $delivery)
    {
    	
    		$em = $this->getDoctrine()->getManager();
    		$em->remove($delivery);
    		$em->flush($delivery);
    	
    
    	return $this->redirectToRoute('delivery_index');
    }
    
    public function activateDeliveryAction(Delivery $delivery)
    {
    	$delivery->activateStatus();
    	$this->getDoctrine()->getManager()->flush($delivery);
    	return $this->redirectToRoute('delivery_index');
    }
    
    public function updateProductDeliveryAction(Delivery $delivery, Product $product, $action='plus', $qty=1)
    {
    	$em = $this->getDoctrine()->getManager();
    	
    	// search already existing product for this delivery
    	$dp = $em->getRepository('InventoryBundle:DeliveryProduct')
    		->getExistingDeliveryProduct($delivery, $product);
    		
    	if($dp) {
    		if($action=='plus') $dp->setQuantity($dp->getQuantity()+$qty);
    		if($action=='minus') {
    			if( ($dp->getQuantity() - $qty) <= 0) {
    				$em->remove($dp);
    			} else {
    				$dp->setQuantity($dp->getQuantity()-$qty);
    			}
    		}
    	} else {
    		if($action=='plus') {
		    	$dp = new DeliveryProduct();
		    	$dp->setDelivery($delivery);
		    	$dp->setProduct($product);
		    	$dp->setUnit($product->getUnit());
		    	$dp->setDeliveryCostPrice($product->getCostPrice());
		    	$dp->setQuantity($qty);
    		
	    		$em->persist($dp);
    		}
    	}
    	
    	$em->flush($dp);
    	
    	return $this->redirectToRoute('delivery_index');
    }

    /**
     * Creates a form to delete a delivery entity.
     *
     * @param Delivery $delivery The delivery entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Delivery $delivery)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('delivery_delete', array('id' => $delivery->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
