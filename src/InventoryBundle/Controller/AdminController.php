<?php

namespace InventoryBundle\Controller;

use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as EasyAdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use InventoryBundle\Entity\Product;

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
}