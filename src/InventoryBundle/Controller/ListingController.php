<?php

namespace InventoryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ListingController extends Controller
{
    public function printBarcodeAction()
    {
        return $this->render('InventoryBundle:Listing:print_barcode.html.twig', array(
            // ...
        ));
    }

    public function printSiteResumeAction()
    {
        return $this->render('InventoryBundle:Listing:print_site_resume.html.twig', array(
            // ...
        ));
    }

}
