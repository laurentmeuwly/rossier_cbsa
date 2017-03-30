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
			$action = $data['action'];
			switch($action) {
				case 'RETOUR':
					//return $this->redirectToRoute('category_index');
					return $this->render('InventoryBundle::chantier.html.twig');
					break;
				case 'SORTIE':
					//return $this->redirectToRoute('site_index');
					return $this->render('InventoryBundle::chantier.html.twig');
					break;
				default:
					return $this->redirectToRoute('depot');
			}
		}
		
		if(isset($data['site'])) {
			if($data['site']=='ANNULER'){
				return $this->redirectToRoute('depot');
			}
			$site = $em->getRepository('InventoryBundle:Site')
			->findOneByName($data['site']);
			if($site ) {
				//&& $site->getStatus=='En cours'
				return $this->redirectToRoute('delivery_new', array('site' => $site->getId()));
			} else {
				// erreur
				return $this->render('InventoryBundle::chantier.html.twig');
			}
		}
	}
	
	
	public function livraisonAction(Request $request)
    {
    	return $this->render('InventoryBundle::layout.html.twig');
    }
    
  

}