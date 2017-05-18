<?php

namespace InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
	 * @var DeliveredProduct[]
	 * 
	 * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\DeliveryProduct", mappedBy="delivery", orphanRemoval=true) 
	 */
	private $deliveredProducts;

	
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
    }
    
    /** {@inheritdoc} */
    public function __toString()
    {
    	return 'Livraison du '. $this->getDeliveryDate()->format('d.m.Y H:i');
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
    
    	foreach($this->deliveredProducts as $dp)
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
     * @param DeliveredProducts[] $deliveredProducts
     */
    public function setDeliveredProducts($deliveredProducts)
    {
    	$this->deliveredProducts = $deliveredProducts;
    }
    
    /**
     * @return DeliveredProducts[]
     */
    public function getDeliveredProducts()
    {
    	return $this->deliveredProducts;
    }
    
    public function addDeliveryProduct($DeliveryProduct)
    {
    	$this->deliveredProducts[] = $DeliveryProduct;
    }
    
    public function removeDeliveryProduct($DeliveryProduct)
    {
    	return $this->deliveredProducts->removeElement($DeliveryProduct);
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
    
    	foreach ($this->getDeliveredProducts() as $item) {
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

