<?php 

namespace InventoryBundle\Utils;

use Hackzilla\BarcodeBundle\Utility\Barcode;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class Barcode
{
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