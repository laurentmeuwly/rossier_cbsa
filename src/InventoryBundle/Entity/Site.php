<?php

namespace InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Site
 *
 * @ORM\Table(name="site")
 * @ORM\Entity(repositoryClass="InventoryBundle\Repository\SiteRepository")
 * @UniqueEntity("name")
 */
class Site
{	
	/**
	 * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\SiteStatus")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $status;
	
	/**
	 * @var Delivery[]
	 * 
	* @ORM\OneToMany(targetEntity="InventoryBundle\Entity\Delivery", mappedBy="site")
	*/
	private $deliveries;
	
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @var int
     *
     * @ORM\Column(name="postcode", type="smallint", nullable=true)
     */
    private $postcode;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     */
    private $city;
    
    /**
     * @var bool
     * 
     * @ORM\Column(name="to_be_printed", type="boolean")
     */
    private $toBePrinted;
    
    
    public function __construct()
    {
    	//$this->deliveries[] = new ArrayCollection();	
    }
    
    public function __toString()
    {
    	return (string)$this->getName();
    }
    
    public function setStatus(SiteStatus $status)
    {	
    	$this->status = $status;
    }
    
    public function getStatus()
    {
    	return $this->status;
    }
    
    public function getStatusName() {
    	return (string)$this->getStatus();
    }
    
    /**
     * @return array
     */
    public static function getAvailableStatus()
    {
    	return array_keys(self::getStatus());
    }
    
    
    public function addDelivery(Delivery $delivery)
    {
    	$this->deliveries[] = $delivery;
    	
    	// link the delivery to the site
    	$delivery->setSite($this);
    }
    
    public function removeDelivery(Delivery $delivery)
    {
    	$this->deliveries->removeElement($delivery);
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
     * Set name
     *
     * @param string $name
     *
     * @return Site
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Site
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set postcode
     *
     * @param integer $postcode
     *
     * @return Site
     */
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;

        return $this;
    }

    /**
     * Get postcode
     *
     * @return int
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Site
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }
    
    public function getDeliveries()
    {
    	return $this->deliveries;
    }
    
    
    public function getDeliveries2()
    {
    	return $this->deliveries . ' voir';
    }
    
    
    	
    public function setToBePrinted($toBePrinted)
    {
    	$this->toBePrinted = $toBePrinted;
    }
    
    public function getToBePrinted()
    {
    	return $this->toBePrinted;
    }
    

}

