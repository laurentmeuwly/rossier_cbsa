<?php

namespace InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="InventoryBundle\Repository\CategoryRepository")
 * @UniqueEntity("name")
 * @Vich\Uploadable
 */
class Category
{
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
     * The category parent.
     *
     * @var Category
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     **/
    private $parent;

    /**
     * @var int
     *
     * @ORM\Column(name="display_order", type="integer", nullable=true)
     */
    private $displayOrder = 100;
    
    /**
     * It only stores the name of the image associated with the category.
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $image;
    
    /**
     * This unmapped property stores the binary contents of the image file
     * associated with the category.
     *
     * @Vich\UploadableField(mapping="category_images", fileNameProperty="image")
     *
     * @var File
     */
    private $imageFile;

    /**
     * Products in the category.
     *
     * @var Product[]
     * @ORM\OneToMany(targetEntity="Product", mappedBy="category")
     **/
    private $products;
    
    
    public function __construct()
    {
    	$this->products = new ArrayCollection();
    }
    
    /**
     * Get the name of the category.
     *
     * @return string
     */
    public function __toString()
    {
    	return $this->getName();
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
     * @return Category
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
     * Set displayOrder
     *
     * @param integer $displayOrder
     *
     * @return Category
     */
    public function setDisplayOrder($displayOrder)
    {
        $this->displayOrder = $displayOrder;

        return $this;
    }

    /**
     * Get displayOrder
     *
     * @return int
     */
    public function getDisplayOrder()
    {
        return $this->displayOrder;
    }
    
    public function setParent(Category $parent=NULL)
    {
    	$this->parent = $parent;
    	return $this;
    }
    
    
    public function getParent()
    {
    	return $this->parent;
    }
    
    /**
     * @param File $image
     */
    public function setImageFile(File $image = null)
    {
    	$this->imageFile = $image;
    	
    	if ($image) {
    		$this->image = $image->getFileName();
    	}
    }
    
    /**
     * @return File
     */
    public function getImageFile()
    {
    	return $this->imageFile;
    }
    
    /**
     * @param string $image
     */
    public function setImage($image)
    {
    	$this->image = $image;
    }
    
    /**
     * @return string
     */
    public function getImage()
    {
    	return $this->image;
    }
    
    /**
     * Return all product associated to the category.
     *
     * @return Product[]
     */
    public function getProducts()
    {
    	return $this->products;
    }
    
    /**
     * Set all products in the category.
     *
     * @param Product[] $products
     */
    public function setProducts($products)
    {
    	$this->products->clear();
    	$this->products = new ArrayCollection($products);
    }
    
    /**
     * Add a product in the category.
     *
     * @param $product Product The product to associate
     */
    public function addProduct($product)
    {
    	if ($this->products->contains($product)) {
    		return;
    	}
    
    	$this->products->add($product);
    	$product->setCategory($this);
    }
    
    /**
     * @param Product $product
     */
    public function removeProduct($product)
    {
    	if (!$this->products->contains($product)) {
    		return;
    	}
    
    	$this->products->removeElement($product);
    	$product->removeCategory($this);
    }
}

