<?php

namespace InventoryBundle\Controller;

use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as EasyAdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use InventoryBundle\Repository\CategoryRepository;
use InventoryBundle\Entity\Product;
use InventoryBundle\Entity\Category;
use InventoryBundle\Entity\Site;
use InventoryBundle\Entity\Delivery;
use InventoryBundle\Entity\DeliveryProduct;
use Hackzilla\BarcodeBundle\Utility\Barcode;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

use InventoryBundle\Form\TestType;


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
    
    public function editDeliveryAction()
    {
    	$response = parent::editAction();
    	
    	if ($response instanceof RedirectResponse)
    	{
    		return $this->redirectToRoute('admin', ['entity' => 'Delivery', 'action' => 'edit', 'id' => $this->request->query->get('id')]);
    	}
    	return $response;
    }
    
    public function preRemoveDeliveryEntity(Delivery $delivery)
    {
    	// as we change the content of a delivery, we have to update the stock of each product involved in this delivery
    	
    	$em = $this->getDoctrine()->getManager();
    	$uow = $em->getUnitOfWork();
    	// rollback current stock information
    	$originalData = $uow->getOriginalEntityData($delivery);
    	$odps = $originalData['deliveryProducts'];
    	//var_dump($odps);
    	foreach($odps as $odp)
    	{
    		$originalDP = $uow->getOriginalEntityData($odp);
    		$p = $odp->getProduct();
    	
    		if(isset($originalDP['quantity'])) {
    			if($originalData['docType'] == 'SORTIE') {
    				$p->setStock($p->getStock() + $originalDP['quantity']);
    			} else {
    				$p->setStock($p->getStock() - $originalDP['quantity']);
    			}
    			$em->flush($p);
    		}
    	
    	}

    }
    
    public function preUpdateDeliveryEntity(Delivery $delivery)
    {
    	// as we change the content of a delivery, we have to update the stock of each product involved in this delivery
    	    	
    	$em = $this->getDoctrine()->getManager();
    	$uow = $em->getUnitOfWork();

    	// rollback current stock information
    	$originalData = $uow->getOriginalEntityData($delivery);
    	
    	$deletedProducts = $delivery->getDeliveryProducts()->getDeleteDiff();
    	foreach($deletedProducts as $ddp)
    	{
    		$p = $ddp->getProduct();
    	
    		if($originalData['docType'] == 'SORTIE') {
    			$p->setStock($p->getStock() + $ddp->getQuantity());
    		} else {
    			$p->setStock($p->getStock() - $ddp->getQuantity());
    		}
    		$em->flush($p);
    	
    	}
    	
    	$odps = $originalData['deliveryProducts'];
    	foreach($odps as $odp)
    	{
    		$originalDP = $uow->getOriginalEntityData($odp);
    		$p = $odp->getProduct();
    		
    		if(isset($originalDP['quantity'])) {
    			if($originalData['docType'] == 'SORTIE') {
	    			$p->setStock($p->getStock() + $originalDP['quantity']);
	    		} else {
	    			$p->setStock($p->getStock() - $originalDP['quantity']);
	    		}
	    		$em->flush($p);
    		}
    		
    	}
    	
    	if(isset($this->request->get('delivery')['deliveryProducts'])) {
    		$dps = $this->request->get('delivery')['deliveryProducts'];
    	
	    	foreach($dps as $dp)
	    	{
	    		$product = $em->getRepository('InventoryBundle:Product')->findOneById($dp['product']);
	    		
	    		if($dp['deliveryCostPrice']=='') {
	    			$dp['deliveryCostPrice'] = $product->getCostPrice();
	    		}
	    		
	    		// update stock information
	    		if($this->request->get('delivery')['docType'] == 'SORTIE') {
    				$product->setStock($product->getStock() - $dp['quantity']);
	    		} else {
    				$product->setStock($product->getStock() + $dp['quantity']);
	    		}
    			$em->flush($product);
	    		
	    	}
    	
    	}
    	
    	
    }
    
    public function prePersistProductEntity($entity)
    {   	
    	$barcode = $entity->getInBarcode();
    	$this->generateBarcodeImg($barcode);
    	$barcode = $entity->getOutBarcode();
    	$this->generateBarcodeImg($barcode);
    	
    }
    
    public function newProductAction()
    {
    	try {
    		return parent::newAction();
    	} catch(CustomForbiddenActionException $e) {
    		$session = $this->request->getSession();
    		$session->getFlashBag()->add('error', $e->getMessage());
    		$refererUrl = $this->request->query->get('referer', '');
    		return  !empty($refererUrl)
    		? $this->redirect(urldecode($refererUrl))
    		: $this->redirect($this->generateUrl('easyadmin', array('action' => 'list', 'entity' => $this->entity['name'])));
    	}
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
     * @Route("/impressum", name="impressum")
     */
    
    public function impressumAction()
    {
    	return $this->render('::impressum.html.twig');
    }
    
    /**
     * @Route("/docs", name="docs")
     */
    
    public function docsAction()
    {
    	$files=array();
    	$dir = $this->container->getParameter('web_dir') . $this->container->getParameter('app.path.docs');
    	$d = opendir($dir."/.");
    	
    	while($item = readdir($d))
    	{
    		if(is_file($sub = $dir."/".$item)) {
	    		$files[] = array(
	    				"name" => "$item",
	    				"url" => $this->container->getParameter('app.path.docs') . "/$item",
	    		);
    		}
    	}
    	
    	return $this->render('::docs.html.twig', array('files' => $files));
    }  
    
    /**
     * Store code as a png image
     */
    public function generateBarcodeImg($code, $filename=NULL)
    {
    	$barcode = $this->get('hackzilla_barcode');
    	$barcode->setMode(Barcode::MODE_PNG);
    	
    	if($filename==NULL) {
    		$filename = $code.'.png';
    	} else {
    		$filename .= '.png';
    	}
    	
    	
    	$fs = new FileSystem();
    	$path = $this->container->getParameter('web_dir') . $this->container->getParameter('app.path.barcodes') . '/';
    	
    	if(!$fs->exists($path)) {
    		try {
    			$fs->mkdir($path, 0770);
    		} catch (IOExceptionInterface $e) {
    			echo "An error occurred while creating your directory at ".$e->getPath();
    		}
    	}
    	
    	return $barcode->save($code, $path . $filename);
    }
}