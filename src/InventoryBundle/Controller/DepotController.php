<?php

namespace InventoryBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use InventoryBundle\Entity\Site;

class DepotController extends Controller
{
	public function dispatchAction(Request $request)
	{
		$data = $request->request->all();
		
		// Getting doctrine manager
		$em = $this->getDoctrine()->getManager();
		
		if(isset($data['action'])) {
			$error = null;
			$action = $data['action'];
			switch($action) {
				case 'RETOUR':
					//return $this->redirectToRoute('category_index');
					return $this->render('InventoryBundle::chantier.html.twig', array('docType' => $action));
					break;
				case 'SORTIE':
					//return $this->redirectToRoute('site_index');
					return $this->render('InventoryBundle::chantier.html.twig', array('docType' => $action));
					break;
				default:
					//return $this->redirectToRoute('depot');
					$error = 'Action inconnue! Réessayez.';
					return $this->renderDepot(array(
							'error' => $error
					));
			}
		}
		
		if(isset($data['site'])) {
			if($data['site']=='ANNULER'){
				return $this->redirectToRoute('depot');
			}
			$site = $em->getRepository('InventoryBundle:Site')
						->getSiteWithStatus($data['site'], array('En cours'));
			if($site ) {
				//return $this->redirectToRoute('delivery_new', array('site' => $site->getId()));
				return $this->forward('InventoryBundle:Delivery:create', array('site' => $site, 'docType' => $data['docType'] ));
			} else {
				// erreur
				$error = 'Chantier invalide.';
				return $this->renderChantier(array(
						'error' => $error
				));
			}
		}
	}
	
	public function livraisonAction(Request $request)
	{
		$qty = 0; // default, each selected product is added/removed by 1 unit
		
		$data = $request->request->all();
	
		// Getting doctrine manager
		$em = $this->getDoctrine()->getManager();
		
		$delivery = $em->getRepository('InventoryBundle:Delivery')
		->findOneById($data['delivery']);
		
		if(isset($data['product'])) {
			$error = null;
			$input = $data['product'];
			switch($input) {
				case 'ANNULER':
					// back to 1st step.
					// don't forget to delete the current delivery order !!
					$response = $this->forward('InventoryBundle:Delivery:customDelete', array('delivery' => $delivery ));
					return $this->redirectToRoute('depot');
					break;
				case 'TERMINER':
					// back to 1st step.
					// don't forget to change the status of the delivery (validate) !!
					// $this->validateDelivery($params)
					$response = $this->forward('InventoryBundle:Delivery:activateDelivery', array('delivery' => $delivery ));
					return $this->redirectToRoute('depot');
					break;
				default:
					// should be an valid product EAN.
					// test product validity and return correct action regarding the case
					
					$product = $em->getRepository('InventoryBundle:Product')
						->getActiveProductByEANCode($input, 'IN');
						
					if($product) {
						$action = 'plus';	
					} else {
						$product = $em->getRepository('InventoryBundle:Product')
							->getActiveProductByEANCode($input, 'OUT');
						
							if($product) {
								$action = 'minus';
							} else {
								// product not found
								$error = 'Article invalide! Réessayez.';
								return $this->redirectToRoute('delivery_show', array('id' => $data['delivery'],
										'error' => $error
								));
						}
					}
					
					if(isset($data['qty'])) {
						$qty = $data['qty'];
					} else {
						$qty=1;
					}
					
					// when here, the product is existing
					if($product->getIsManualAllowed() && !isset($data['qty'])) {
						$response = $this->forward('InventoryBundle:Delivery:show',
								array('id' => $data['delivery'],
										'product' => $product,
										'ean' => $data['product'],
										'setQty' =>true,
										'error' => $error,
						));
						return $response;	
					} else {
						$response = $this->forward('InventoryBundle:Delivery:updateProductDelivery', 
							array('delivery' => $delivery, 
									'product' => $product,
									'action' => $action,
									'qty' => $qty,
							));
					}
					
					
					return $this->redirectToRoute('delivery_show', array('id' => $data['delivery'],
							'error' => $error
					));
					
			}
		}
	}
	
	
	public function livraison2Action(Request $request)
    {
    	return $this->render('InventoryBundle::layout.html.twig');
    }
    
    protected function renderDepot(array $data)
    {
    	return $this->render('::depot.html.twig', $data);
    }
    
    protected function renderChantier(array $data)
    {
    	return $this->render('InventoryBundle::chantier.html.twig', $data);
    }
    
    protected function renderDelivery(array $data)
    {
    	return $this->render('InventoryBundle:Delivery:show.html.twig', $data);
    }

}