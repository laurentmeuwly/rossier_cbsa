<?php

namespace InventoryBundle\Controller;

use InventoryBundle\Entity\Product;
use InventoryBundle\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Hackzilla\BarcodeBundle\Utility\Barcode;

/**
 * Product controller.
 *
 */
class ProductController extends Controller
{
    /**
     * Lists all product entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository('InventoryBundle:Product')->findAll();

        return $this->render('InventoryBundle:Product:index.html.twig', array(
            'products' => $products,
        ));
    }

    /**
     * Creates a new product entity.
     *
     */
    public function newAction(Request $request)
    {
        $product = new Product();
        $product->setInBarcode('e-12357');
        
        $form = $this->createForm('InventoryBundle\Form\ProductType', $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        	
        	/* opérations spécifiques à mettre ici */
        	$product->setDisplayOrder(1);
        	
        	/* image */
        	$image = new Image();
        	$image->setUrl('http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg');
        	$image->setAlt('Job de rêve');
        	$product->setImage($image);
        	
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush($product);

            return $this->redirectToRoute('product_show', array('id' => $product->getId()));
        }

        return $this->render('InventoryBundle:Product:new.html.twig', array(
            'product' => $product,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a product entity.
     *
     */
    public function showAction(Product $product)
    {
        $deleteForm = $this->createDeleteForm($product);

        return $this->render('InventoryBundle:Product:show.html.twig', array(
            'product' => $product,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing product entity.
     *
     */
    public function editAction(Request $request, Product $product)
    {
        $deleteForm = $this->createDeleteForm($product);
        $editForm = $this->createForm('InventoryBundle\Form\ProductType', $product);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('product_edit', array('id' => $product->getId()));
        }

        return $this->render('InventoryBundle:Product:edit.html.twig', array(
            'product' => $product,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a product entity.
     *
     */
    public function deleteAction(Request $request, Product $product)
    {
        $form = $this->createDeleteForm($product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush($product);
        }

        return $this->redirectToRoute('product_index');
    }

    /**
     * Creates a form to delete a product entity.
     *
     * @param Product $product The product entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Product $product)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('product_delete', array('id' => $product->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Display code using html
     */
    public function barcodeHtmlAction(Product $product)
    {
    	$code = $product->getId();
    	$barcode = $this->get('hackzilla_barcode');
    
    	$headers = array(
    	);
    
    	return new Response($barcode->outputHtml($code), 200, $headers);
    }
}
