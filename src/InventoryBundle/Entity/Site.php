<?php

namespace InventoryBundle\Entity;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use InventoryBundle\Form\SiteStatusType;

/**
 * Site
 *
 * @ORM\Table(name="site")
 * @ORM\Entity(repositoryClass="InventoryBundle\Repository\SiteRepository")
 */
class Site
{
	const STATUS_RUNNING = "En cours";
	const STATUS_TERMINATED = "Terminé";
	
	public static $siteStatus = array(
		Site::STATUS_RUNNING => "En cours",
		Site::STATUS_TERMINATED => "Terminé",
	);
	
	
	/**
	 * @var string
	 * 
	 * @ORM\Column(name="status", type="string", length=255, nullable=true)
	 * 
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
    
    public function setStatus($status)
    {
    	if(!in_array($status, SiteStatusType::getAvailableStatus())) {
    		throw new \InvalidArgumentException('Invalid status');
    	}
    	
    	$this->status = $status;
    	return $this;
    }
    
    public function getStatus()
    {
    	if(!is_null($this->status)) {
    		return self::$siteStatus[$this->status];
    	} else {
    		return null;
    	}
    }
    
    public static function getStatusList() {
    	return self::$siteStatus;
    }
    
    public function getStatusName() {
    	return (string)$this->getStatus();
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

