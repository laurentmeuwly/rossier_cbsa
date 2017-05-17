<?php

namespace InventoryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Hackzilla\BarcodeBundle\Utility\Barcode;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use InventoryBundle\Entity\Category;
use InventoryBundle\Entity\Product;
use InventoryBundle\Entity\Unit;

class ListingController extends Controller
{
	public function generateImgBarcodeAction()
	{
		$em = $this->getDoctrine()->getManager();
		$products = $em->getRepository('InventoryBundle:Product')->findAll();
		
		$bc = $this->container->get('app.barcode');
		
		foreach($products as $product) {
			if(strlen($product->getInBarcode())==13) {
				$this->generateBarcodeImage($product->getInBarcode());
			}
			if(strlen($product->getOutBarcode())==13) {
				$this->generateBarcodeImage($product->getOutBarcode());
			}
		}
		
		$sites = $em->getRepository('InventoryBundle:Site')->findAll();
		foreach($sites as $site) {
			$code = str_pad($site->getId(), 12, "0", STR_PAD_RIGHT);
			$code .= $bc->eanCheckDigit($code);
			$this->generateBarcodeImage($code, 'site_' . $site->getId());
		}
		return $this->render('::impressum.html.twig');
	}
	
    public function printProductsBookAction()
    {
    	$filePath = '/var/www/test.pdf';
    	
    	$em = $this->getDoctrine()->getManager();
    	$categories = $em->getRepository('InventoryBundle:Category')->findAll();
    	//$products = $em->getRepository('InventoryBundle:Product')->findAll();
    	$products = $em->getRepository('InventoryBundle:Product')->findBy(
    		array('toBePrinted' => true),
    		array('name' => 'ASC')
    			);
    	
    	$html = $this->renderView('InventoryBundle:Listing:print_products_book.html.twig', array(
    			'products' => $products
    	));
    	
    	// remove old file
    	if (file_exists($filePath)) {
    		unlink($filePath);
    	}
    	
    	return new Response(
    			$this->get('knp_snappy.pdf')->getOutputFromHtml($html),
    			200,
    			array(
    					'Content-Type'          => 'application/pdf',
    					'Content-Disposition'   => 'attachment; filename="file.pdf"'
    			)
    			);
    }
    
    public function printSitesBookAction()
    {
    	$filePath = '/var/www/test.pdf';
    	 
    	$em = $this->getDoctrine()->getManager();
    	$sites = $em->getRepository('InventoryBundle:Site')->findBy(
    			array('toBePrinted' => true),
    			array('name' => 'ASC')
    			);

    	$html = $this->renderView('InventoryBundle:Listing:print_sites_book.html.twig', array(
    			'sites' => $sites
    	));
    	
    	// remove old file
    	if (file_exists($filePath)) {
    		unlink($filePath);
    	}

    	return new Response(
    			$this->get('knp_snappy.pdf')->getOutputFromHtml($html),
    			200,
    			array(
    					'Content-Type'          => 'application/pdf',
    					'Content-Disposition'   => 'attachment; filename="file.pdf"'
    			)
    			);

    }

    public function printSiteResumeAction($id=NULL, $report='perDeliveries')
    {
    	// $report = perDeliveries or perProducts. Default = perDeliveries
    	$filePath = '/var/www/test.pdf';
    	
    	$em = $this->getDoctrine()->getManager();
    	
    	$sumCost = 0;
    	$sumSale = 0;
    	
    	$site = $em->getRepository('InventoryBundle:Site')->findOneBy(
    			array('id' => $id)
    			);
    	
    	
    	
    	if($report=='perProducts') {
    		$deliveries = $em->getRepository('InventoryBundle:Delivery')->findBySite(
    				array('id' => $id)
    				);
    		$tProd = array();
    		foreach($deliveries as $delivery) {
    			foreach($delivery->getDeliveredProducts() as $product) {
    				$sumCost += $product->getQuantity() * $product->getDeliveryCostPrice();
    				$sumSale += $product->getQuantity() * $product->getProduct()->getSalePrice();
    				
    				if(!array_key_exists($product->getProduct()->getId(), $tProd)) {
    					$tProd[$product->getProduct()->getId()]['product'] = $product->getProduct();
    					$tProd[$product->getProduct()->getId()]['quantity'] = $product->getQuantity();
    					$tProd[$product->getProduct()->getId()]['totalCostPrice'] = ($product->getDeliveryCostPrice() * $product->getQuantity());
    				} else {
	    				$tProd[$product->getProduct()->getId()]['quantity'] += $product->getQuantity();
	    				$tProd[$product->getProduct()->getId()]['totalCostPrice'] += ($product->getDeliveryCostPrice() * $product->getQuantity());
    				}
    			}
    		}
    		$html = $this->renderView('InventoryBundle:Listing:print_site_resume_per_products.html.twig', array(
    				'site' => $site, 'products' => $tProd, 'sumCost' => $sumCost, 'sumSale' => $sumSale
    		));
    	} else {
    		if($site) {
    			foreach($site->getDeliveries() as $delivery) {
    				foreach($delivery->getDeliveredProducts() as $product) {
    					$sumCost += $product->getQuantity() * $product->getDeliveryCostPrice();
    					$sumSale += $product->getQuantity() * $product->getProduct()->getSalePrice();
    				}
    			}
    		}
    		
	    	$html = $this->renderView('InventoryBundle:Listing:print_site_resume_per_deliveries.html.twig', array(
	    			'site' => $site, 'sumCost' => $sumCost, 'sumSale' => $sumSale
	    	));
    	}
        
    	return new Response(
    			$this->get('knp_snappy.pdf')->getOutputFromHtml($html),
    			200,
    			array(
    					'Content-Type'          => 'application/pdf',
    					'Content-Disposition'   => 'attachment; filename="file.pdf"'
    			)
    			);
    }
    
    
    
    public function existsBarcodeImage($code)
    {
    	$fs = new FileSystem();
    	$path = $this->container->getParameter('web_dir') . $this->container->getParameter('app.path.product_images') . '/barcode/';
    	$filename = $code.'.png';
    
    	return $fs->exists($path . $filename);
    }
    
    public function generateBarcodeImage($code, $filename=NULL)
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
