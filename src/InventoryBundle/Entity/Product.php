<?php

namespace InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="InventoryBundle\Repository\ProductRepository")
 */
class Product
{
    /** 
     * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\DeliveryProduct", mappedBy="product")
     */
	private $deliveredProducts;
	
	/**
	 * @ORM\OneToOne(targetEntity="InventoryBundle\Entity\Image", cascade={"persist", "remove"})
	 */
	private $image;
	
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
     *
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     **/
    
      private $active = true;
    
    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=255)
     */
    private $comment;

    /**
     * @var float 
     *
     * @ORM\Column(name="cost_price", type="float")
     */
    private $costPrice;

    /**
     * @var float 
     *
     * @ORM\Column(name="sale_price", type="float")
     */
    private $salePrice;

    /**
     * @var string
     *
     * @ORM\Column(name="in_barcode", type="string", length=100, unique=true)
     */
    private $inBarcode;

    /**
     * @var string
     *
     * @ORM\Column(name="out_barcode", type="string", length=100, unique=true)
     */
    private $outBarcode;


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
     * @return Product
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
     * Set comment
     *
     * @param string $comment
     *
     * @return Product
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set costPrice
     *
     * @param string $costPrice
     *
     * @return Product
     */
    public function setCostPrice($costPrice)
    {
        $this->costPrice = $costPrice;

        return $this;
    }

    /**
     * Get costPrice
     *
     * @return string
     */
    public function getCostPrice()
    {
        return $this->costPrice;
    }

    /**
     * Set salePrice
     *
     * @param string $salePrice
     *
     * @return Product
     */
    public function setSalePrice($salePrice)
    {
        $this->salePrice = $salePrice;

        return $this;
    }

    /**
     * Get salePrice
     *
     * @return string
     */
    public function getSalePrice()
    {
        return $this->salePrice;
    }

    /**
     * Set inBarcode
     *
     * @param string $inBarcode
     *
     * @return Product
     */
    public function setInBarcode($inBarcode)
    {
        $this->inBarcode = $inBarcode;

        return $this;
    }

    /**
     * Get inBarcode
     *
     * @return string
     */
    public function getInBarcode()
    {
        return $this->inBarcode;
    }

    /**
     * Set outBarcode
     *
     * @param string $outBarcode
     *
     * @return Product
     */
    public function setOutBarcode($outBarcode)
    {
        $this->outBarcode = $outBarcode;

        return $this;
    }

    /**
     * Get outBarcode
     *
     * @return string
     */
    public function getOutBarcode()
    {
        return $this->outBarcode;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Product
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }
    
    public function setImage(Image $image=null)
    {
    	$this->image = $image;
    }
    
    public function getImage()
    {
    	return $this->image;
    }
}
