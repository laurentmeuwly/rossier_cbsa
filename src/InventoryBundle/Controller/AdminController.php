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
    
    public function preUpdateDeliveryEntity(Delivery $delivery)
    {
    	$dps = $this->request->get('delivery')['deliveryProducts'];
    	$em = $this->getDoctrine()->getManager();
    	
    	foreach($dps as $dp)
    	{
    		$product = $em->getRepository('InventoryBundle:Product')->findOneById($dp['product']);
    		//var_dump($product->getCostPrice());
    		if($dp['deliveryCostPrice']=='') {
    			$dp['deliveryCostPrice'] = $product->getCostPrice();
    		}
    		//$this->request->get('delivery')['deliveryProducts'][$i]['deliveryCostPrice'] = $product->getCostPrice();
    		//$dp['deliveryCostPrice'] = $product->getCostPrice();
    		
    	}
    	
    	//var_dump($this->request->get('delivery')['deliveryProducts']);
    	
    }
    
    public function prePersistProductEntity($entity)
    {
    	$barcode = $entity->getInBarcode();
    	$this->generateBarcodeImg($barcode);
    	$barcode = $entity->getOutBarcode();
    	$this->generateBarcodeImg($barcode);
    	
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