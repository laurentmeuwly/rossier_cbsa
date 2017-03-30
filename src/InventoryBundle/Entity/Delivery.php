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
	 * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\DeliveryProduct", mappedBy="delivery") 
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
     * @ORM\Column(name="delivery_date", type="date")
     */
    private $deliveryDate;


    public function __construct()
    {
    	$this->deliveryDate = new \Datetime();
    }
    
    public function __toString()
    {
    	return (string)$this->getId();
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
    
    		$dp->setOrder($this);
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
    
    public function getTotalCost()
    {
    	$total = 0.0;
    
    	foreach ($this->getDeliveredProducts() as $item) {
    		$total += $item->getTotalCostPrice();
    	}
    
    	return $total;
    }
}

