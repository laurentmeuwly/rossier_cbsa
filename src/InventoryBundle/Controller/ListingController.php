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
		
		foreach($products as $product) {
			if(strlen($product->getInBarcode())==13) {
				$this->generateBarcodeImage($product->getInBarcode());
			}
			if(strlen($product->getOutBarcode())==13) {
				$this->generateBarcodeImage($product->getOutBarcode());
			}
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
    	
    	/*foreach($products as $product) {
    		if($product->getImage()=='') {
    			$product->setImage($product->getCategory()->getImage());
    		}
    	}*/
    	
    	/* $html = $this->renderView ...*/
    	$html = $this->renderView('InventoryBundle:Listing:print_products_book.html.twig', array(
    			'products' => $products
    	));
    	/*<td>{% if product.image is not null and product.image != '' %}
                <img src="{{ absolute_url(asset('/uploads/images/products/' ~ product.image )) }}"  width="100px" height="50px"/>
                 
        {%  endif %}
    </td>*/
    	// remove old file
    	if (file_exists($filePath)) {
    		unlink($filePath);
    	}

    	$url = 'https://wiki.monitoring-fr.org/securite/architecture-oss/start';
    	
    	//$this->get('knp_snappy.pdf');
    	//$this->get('knp_snappy.pdf')->generateFromHtml($html, $filePath);
    	
    	return new Response(
    			$this->get('knp_snappy.pdf')->getOutputFromHtml($html),
    			200,
    			array(
    					'Content-Type'          => 'application/pdf',
    					'Content-Disposition'   => 'attachment; filename="file.pdf"'
    			)
    			);
    	
    	/*return new Response(
    			$this->get('knp_snappy.pdf')->getOutputFromHtml($html),
    			200,
    			array(
    					'Content-Type'          => 'application/pdf',
    					'Content-Disposition'   => 'attachment; filename="file.pdf"'
    			)
    			);*/
    	
    	// set flash bag message
    	//$this->get('session')->getFlashBag()->add('notice', 'PDF généré');
    	
    	//return $this->redirect($this->generateUrl('inventory_bundle_admin_index'));
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

    public function printSiteResumeAction()
    {
        return $this->render('InventoryBundle:Listing:print_site_resume.html.twig', array(
            // ...
        ));
    }
    
    
    
    public function existsBarcodeImage($code)
    {
    	$fs = new FileSystem();
    	$path = $this->container->getParameter('web_dir') . $this->container->getParameter('app.path.product_images') . '/barcode/';
    	$filename = $code.'.png';
    
    	return $fs->exists($path . $filename);
    }
    
    public function generateBarcodeImage($code)
    {
    	$barcode = $this->get('hackzilla_barcode');
    	$barcode->setMode(Barcode::MODE_PNG);
    
    	$filename = $code.'.png';
    
    
    	$fs = new FileSystem();
    	$path = $this->container->getParameter('web_dir') . $this->container->getParameter('app.path.product_images') . '/barcode/';
    
    	if(!$fs->exists($path)) {
    		try {
    			$fs->mkdir($path, 0700);
    		} catch (IOExceptionInterface $e) {
    			echo "An error occurred while creating your directory at ".$e->getPath();
    		}
    	}
    
    	return $barcode->save($code, $path . $filename);
    }

}
