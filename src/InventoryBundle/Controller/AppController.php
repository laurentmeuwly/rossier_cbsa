<?php

// src/InventoryBundle/Controller/AppController.php

namespace InventoryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Hackzilla\BarcodeBundle\Utility\Barcode;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class AppController extends Controller
{
    /**
     * Display code as a png image
     */
    public function barcodeImageAction($code)
    {
        $barcode = $this->get('hackzilla_barcode');
        $barcode->setMode(Barcode::MODE_PNG);

        $headers = array(
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'inline; filename="'.$code.'.png"'
        );

        return new Response($barcode->outputImage($code), 200, $headers);
    }
    
    /**
     * Store code as a png image
     */
    public function barcodeImgAction($code)
    {
    	$barcode = $this->get('hackzilla_barcode');
    	$barcode->setMode(Barcode::MODE_PNG);
    
    	$filename=$code.'.png';
    	
    	
    	$fs = new FileSystem();
    	$path = $this->container->getParameter('web_dir') . $this->container->getParameter('app.path.product_images') . '/barcode/';
    	
    	if(!$fs->exists($path)) {
    		try {
    			$fs->mkdir($path, 0700);
    		} catch (IOExceptionInterface $e) {
    			echo "An error occurred while creating your directory at ".$e->getPath();
    		}
    	}
    	
    	$file = $barcode->save($code, $path . $filename);
    	//$fs->copy($barcode->returnImage($code), $path . $filename);
    	
    
    	return $this->render('::impressum.html.twig');
    }

    /**
     * Display code using html
     */
    public function barcodeHtmlAction($code)
    {
        $barcode = $this->get('hackzilla_barcode');

        $headers = array(
        );

        return new Response($barcode->outputHtml($code), 200, $headers);
    }


    private function getImage($ean)
    {
        ob_start();

        $barcodeGenerator = new Barcode();
        $barcodeGenerator->setMode(Barcode::mode_png);
        $barcodeGenerator->outputImage($ean);

        $contents = ob_get_clean();

        return $contents;
    }
}