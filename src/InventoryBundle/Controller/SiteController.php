<?php

namespace InventoryBundle\Controller;

use InventoryBundle\Entity\Site;
use InventoryBundle\Entity\Delivery;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManager;

/**
 * Site controller.
 *
 */
class SiteController extends Controller
{
	/**
	 * Test purpose
	 * 
	 */
	public function adminAction()
	{
		return new Response('<html><body>Admin page!</body></html>');
	}
	
    /**
     * Lists all site entities.
     *
     */
    public function indexAction($page=1)
    {
    	$nbPerPage = 2; // !! à mettre dans parameter, et accès via: $this->container->getParameter('nb_per_page')
    	
    	if ($page < 1) {
    		throw $this->createNotFoundException("La page ".$page." n'existe pas.");
    	}
    	
    	$em = $this->getDoctrine()->getManager();

        $sites = $em->getRepository('InventoryBundle:Site')->findAll(); //->getSites($page, $nbPerPage);

        $nbPages = ceil(count($sites) / $nbPerPage);
        
        if ($page > $nbPages) {
        	throw $this->createNotFoundException("La page ".$page." n'existe pas.");
        }
        
        return $this->render('InventoryBundle:Site:index.html.twig', array(
            'sites' => $sites,
        	'nbPages' => $nbPages,
        	'page' => $page,
        ));
    }

    /**
     * Creates a new site entity.
     *
     */
    public function newAction(Request $request)
    {
        $site = new Site();
        $form = $this->createForm('InventoryBundle\Form\SiteType', $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($site);
            $em->flush($site);

            return $this->redirectToRoute('site_show', array('id' => $site->getId()));
        }

        return $this->render('InventoryBundle:Site:new.html.twig', array(
            'site' => $site,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a site entity.
     *
     */
    public function showAction(Site $site)
    {
        $deleteForm = $this->createDeleteForm($site);

        return $this->render('InventoryBundle:Site:show.html.twig', array(
            'site' => $site,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing site entity.
     *
     */
    public function editAction(Request $request, Site $site)
    {
        $deleteForm = $this->createDeleteForm($site);
        $editForm = $this->createForm('InventoryBundle\Form\SiteType', $site);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('site_edit', array('id' => $site->getId()));
        }

        return $this->render('InventoryBundle:Site:edit.html.twig', array(
            'site' => $site,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a site entity.
     *
     */
    public function deleteAction(Request $request, Site $site)
    {
        $form = $this->createDeleteForm($site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($site);
            $em->flush($site);
        }

        return $this->redirectToRoute('site_index');
    }

    /**
     * Creates a form to delete a site entity.
     *
     * @param Site $site The site entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Site $site)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('site_delete', array('id' => $site->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Views deliveries for a site
     * 
     */
    public function viewDeliveriesAction($id)
    {
    	$em = $this->getDoctrine()->getManager();
    	$site = $em->getRepository('InventoryBundle:Site')->find($id);

    	$listDeliveries = $em
    	->getRepository('InventoryBundle:Delivery')
    	->findBy(array('site' => $site),
    			array('deliveryDate' => 'desc')
    	);
    	
    	return $this->render('InventoryBundle:Site:viewdelivery.html.twig', array(
    			'site'           => $site,
    			'listDeliveries' => $listDeliveries
    	));
    }
    
    
    public function pdfAction()
    {
    	$snappy = $this->get('knp_snappy.pdf');
    	$filename = 'myFirstSnappyPDF';
    	$html = '<h1>Hello</h1>';
    	
    	return new Response(
    			$snappy->getOutputFromHtml($html),
    			200,
    			array(
    					'Content-Type'        => 'application/pdf',
    					'Content-Disposition' => 'attachement; filename="' . $filename . '.pdf"',
    			)
    			);
    }

}
