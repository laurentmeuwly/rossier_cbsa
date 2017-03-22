<?php

namespace InventoryBundle\Entity;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Site
 *
 * @ORM\Table(name="site")
 * @ORM\Entity(repositoryClass="InventoryBundle\Repository\SiteRepository")
 */
class Site
{
	/**
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
    
    
    public function __construct()
    {
    	$this->deliveries[] = new ArrayCollection();	
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
    
    
    
    
    /**
     * Get deliveries
     *
     * @return array
     */
    public function getDelivery($siteId)
    {
    	return $siteId;
    }
}

