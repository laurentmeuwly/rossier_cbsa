<?php

namespace InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DeliveryProduct
 *
 * @ORM\Table(name="delivery_product")
 * @ORM\Entity(repositoryClass="InventoryBundle\Repository\DeliveryProductRepository")
 */
class DeliveryProduct
{
	/**
	 * @ORM\Id()
	 * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\Delivery", inversedBy="deliveredProducts")
	 * @ORM\JoinColumn(name="delivery_id", referencedColumnName="id", nullable=false)
	 */
	private $delivery;
	
	/**
	 * @ORM\Id()
	 * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\Product", inversedBy="deliveredProducts")
	 * @ORM\JoinColumn(name="product_id", referencedColumnName="id", nullable=false)
	 */
	private $product;
    
    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;
    
    /**
     * @var float
     *
     * @ORM\Column(name="delivery_cost_price", type="float")
     */
    private $deliveryCostPrice;


    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return DeliveryProduct
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
    
    public function setDeliveryCostPrice($price)
    {
    	$this->deliveryCostPrice = $price;
    }
    
    public function getDeliveryCostPrice()
    {
    	return $this->deliveryCostPrice;
    }
    
    public function setDelivery(Delivery $delivery)
    {
    	$this->delivery = $delivery;
    }
    
    public function getDelivery()
    {
    	return $this->delivery;
    }
    
    public function setProduct(Product $product)
    {
    	$this->product = $product;
    }
    
    public function getProduct()
    {
    	return $this->product;
    }
}

