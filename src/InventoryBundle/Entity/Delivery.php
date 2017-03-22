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
	* @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\Site", inversedBy="deliveries")
	* @ORM\JoinColumn(nullable=false)
	*/
	private $site;
	
	/** @ORM\OneToMany(targetEntity="InventoryBundle\Entity\DeliveryProduct", mappedBy="delivery") */
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
    
    public function setSite(Site $site)
    {
    	$this->site = $site;
    	return $this;
    }
    
    
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
}

