<?php

namespace InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Delivery
 *
 * @ORM\Table(name="delivery")
 * @ORM\Entity(repositoryClass="InventoryBundle\Repository\DeliveryRepository")
 */
class Delivery
{
	/**
	* @var Site
	* 
	* @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\Site", inversedBy="deliveries")
	* @ORM\JoinColumn(nullable=false)
	*/
	private $site;
	
	/** 
	 * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\DeliveryProduct", mappedBy="delivery", cascade={"persist","remove"}, orphanRemoval=true) 
	 */
	private $deliveryProducts;

	
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="delivery_date", type="datetime")
     */
    private $deliveryDate;
    
    /**
     * @var string
     * @ORM\Column(name="doc_type", type="string", length=20)
     */
    private $docType = 'SORTIE';
    
    /**
     * 
     * @var float
     */
    private $totalCost;
    
    /**
     * @var status
     * @ORM\Column(name="status", type="integer")
     */
    private $status = 0;


    public function __construct()
    {
    	$this->deliveryDate = new \Datetime();
    	$this->deliveryProducts = new ArrayCollection();
    }
    
    /** {@inheritdoc} */
    public function __toString()
    {
    	return 'Livraison du '. $this->getDeliveryDate()->format('d.m.Y H:i') . ' - ' . $this->getDocType();
    }
    
    
    public function setSite(Site $site)
    {
    	$this->site = $site;
    	return $this;
    }
    
    /**
     * Get site
     *      
     * @return Site
     */
    public function getSite()
    {
    	return $this->site;
    }
    
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set deliveryDate
     *
     * @param \DateTime $deliveryDate
     *
     * @return Delivery
     */
    public function setDeliveryDate($deliveryDate)
    {
        $this->deliveryDate = $deliveryDate;

        return $this;
    }

    /**
     * Get deliveryDate
     *
     * @return \DateTime
     */
    public function getDeliveryDate()
    {
        return $this->deliveryDate;
    }
    
    public function getProduct()
    {
    	$products = new ArrayCollection();
    
    	foreach($this->deliveryProducts as $dp)
    	{
    		$products[] = $dp->getProduct();
    	}
    
    	return $products;
    }
    
    public function setProduct($products)
    {
    	foreach($products as $p)
    	{
    		$dp = new DeliveryProduct();
    
    		$dp->setDelivery($this);
    		$dp->setProduct($p);
    
    		$this->addDeliveryProduct($dp);
    	}
    
    }
    
        
    /**
     * @return DeliveryProducts[]
     */
    public function getDeliveryProducts()
    {
    	return $this->deliveryProducts;
    }
    
    public function addDeliveryProduct(DeliveryProduct $deliveryProduct)
    {
    	$this->deliveryProducts[] = $deliveryProduct;
    	// On lie la ligne à la livraiosn
    	$deliveryProduct->setDelivery($this);
    	 
    	return $this;
    }
    
    public function removeDeliveryProduct(DeliveryProduct $deliveryProduct)
    {
    	return $this->deliveryProducts->removeElement($deliveryProduct);
    }
    
    public function getSiteName()
    {
    	return $this->site->getName();
    }
    
    public function getStatus()
    {
    	return $this->status;
    }
    
    public function setStatus($status)
    {
    	$this->status = $status;
    }
    
    public function getDocType()
    {
    	return $this->docType;
    }
    
    public function setDocType($docType)
    {
    	$this->docType = $docType;
    }
    
    /**
     * Get totalCost
     *
     * @return float
     */
    public function getTotalCost()
    {
    	$total = 0.0;
    
    	foreach ($this->getDeliveryProducts() as $item) {
    		$total += $item->getTotalCostPrice();
    	}
    
    	return $total;
    }
    
    /**
     * activate status
     * @return integer 
     */
    public function activateStatus()
    {
    	$this->status = 1;
    	return $this->status;
    }
}

