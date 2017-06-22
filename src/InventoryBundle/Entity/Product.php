<?php

namespace InventoryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Hackzilla\BarcodeBundle\Utility\Barcode;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="InventoryBundle\Repository\ProductRepository")
 * @UniqueEntity("name")
 * @Vich\Uploadable
 */
class Product
{
    /**
     * @var DeliveryProduct[]
     * @ORM\OneToMany(targetEntity="InventoryBundle\Entity\DeliveryProduct", mappedBy="product"), cascade={"persist"})
     */
	private $deliveryProducts;
	
	/**
	 * @var Category
	 * 
	* @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\Category", inversedBy="products")
	* @ORM\OrderBy({"name" = "ASC"})
	* @ORM\JoinColumn(nullable=false)
	*/
	private $category;
	
	/**
	 * @ORM\ManyToOne(targetEntity="InventoryBundle\Entity\Unit")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $unit;
	
	/**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var int
     *
     * @ORM\Column(name="display_order", type="integer", nullable=true)
     */
    private $displayOrder = 100;
    

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true, nullable=false)
     */
    private $name;

    /**
     *
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean")
     **/
    
    private $isActive = true;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comment;
    
    /**
     * @var int
     *
     * @ORM\Column(name="stock", type="integer", nullable=true)
     */
    private $stock = 0;

    /**
     * @var float 
     *
     * @ORM\Column(name="cost_price", type="float", nullable=false)
     */
    private $costPrice;

    /**
     * @var float 
     *
     * @ORM\Column(name="sale_price", type="float", nullable=true)
     */
    private $salePrice;

    /**
     * @var string
     *
     * @ORM\Column(name="in_barcode", type="string", length=100, unique=true, nullable=false)
     */
    private $inBarcode;

    /**
     * @var string
     *
     * @ORM\Column(name="out_barcode", type="string", length=100, unique=true, nullable=false)
     */
    private $outBarcode;
    
    /**
     * It only stores the name of the image associated with the product.
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $image;
    
    /**
     * This unmapped property stores the binary contents of the image file
     * associated with the product.
     *
     * @Vich\UploadableField(mapping="product_images", fileNameProperty="image")
     *
     * @var File
     */
    private $imageFile;
    
    /**
     * @var bool
     *
     * @ORM\Column(name="to_be_printed", type="boolean")
     */
    private $toBePrinted;
    
    /**
     * @var bool
     * 
     * @ORM\Column(name="is_manual_allowed", type="boolean")
     */
    private $isManualAllowed = false;
    
    /**
     * 
     * @param string $isActive
     */
    public function __construct($isActive=true)
    {
    	$this->setIsActive($isActive);
    	
    	$tempCode = $this->getPartialBarCode();
    	
    	$this->setInBarcode($this->generateBarCode('add',$tempCode));
    	//$this->generateBarcodeImage($this->getInBarcode());
    	$this->setOutBarcode($this->generateBarCode('del',$tempCode));
    }
    
    /**
     * 
     * @return string
     */
    public function __toString()
    {
    	return (string)$this->getName();
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
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Product
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
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
     * Set displayOrder
     *
     * @param integer $displayOrder
     *
     * @return Product
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
    
    public function setUnit(Unit $unit)
    {
    	$this->unit = $unit;
    }
    
    public function getUnit()
    {
    	return $this->unit;
    }
    
    public function setStock($stock)
    {
    	$this->stock = $stock;
    }
    
    public function getStock()
    {
    	return $this->stock;
    }
    
    public function setCategory(Category $category)
    {
    	$this->category = $category;
    }
    
    public function getCategory()
    {
    	return $this->category;
    }
    
    public function getDeliveryProducts()
    {
    	return $this->deliveryProducts;
    }
    
    public function setDeliveryProducts($dp)
    {
    	$this->deliveryProducts = $dp;
    	return $this;
    }
    
    public function setToBePrinted($toBePrinted)
    {
    	$this->toBePrinted = $toBePrinted;
    }
    
    public function getToBePrinted()
    {
    	return $this->toBePrinted;
    }
    
    public function setIsManualAllowed($isManualAllowed)
    {
    	$this->isManualAllowed = $isManualAllowed;
    }
    
    public function getIsManualAllowed()
    {
    	return $this->isManualAllowed;
    }
    
    public function setInBarCodeImg()
    {	
    }
    
    /**
     * @return string
     */
    public function getInBarcodeImg()
    {
    	$code = $this->getInBarcode();
    	//$this->get('app.barcode')->existsBarcodeImage($code);
    	//$this->existsBarcodeImage($code);
    	/*if($this->existsBarcodeImage($this->getInBarcode())) {
    		//$this->generateBarcodeImage($this->getInBarcode());
    	}*/
    	return $this->getInBarcode() . '.png';
    }
    
    public function setOutBarcodeImg()
    {
    }
    
    /**
     * @return string
     */
    public function getOutBarcodeImg()
    {
    	/*if(!$this->existsBarcodeImage($this->getOutBarcode())) {
    		$this->generateBarcodeImage($this->getOutBarcode());
    	}*/
    	return $this->getOutBarcode() . '.png';
    }
    
    
    private function getPartialBarCode()
    {
    	$temp = new \DateTime();
    	return (string)$temp->getTimeStamp();
    }
    
    
    private function generateBarCode($type, $partialCode)
    {
    	// format: yyxxxxxxxxxxc
    	// yy:
    	// 10 - 19 : réservés pour les actions
    	// 20 - 29 : réservés pour les ajouts
    	// 30 - 39 : réservés pour les suppression
    	// xxxxxxxxxx:
    	// unique, incrémental, pour l'article
    	// c:
    	// code de contrôle
    	if($type=='add') {
    		$yy = '21';
    	} elseif($type=='del') {
    		$yy = '31';
    	}
    	
    	$temp = (string)$yy . (string)$partialCode;
    	return $temp . $this->eanCheckDigit($temp);
    }
    
    private function eanCheckDigit($code)
    {
    	$code = str_pad($code, 12, "0", STR_PAD_LEFT);
    	$sum = 0;
    	
    	for($i=(strlen($code)-1); $i>=0; $i--)
    	{
    		$sum += (($i%2)*2 + 1) * $code[$i];
    	}
    	
    	return (10 - ($sum % 10)) % 10;
    }

    public function existsBarcodeImage($code)
    {
    	$fs = new FileSystem();
    	$path = $this->container->getParameter('web_dir') . $this->container->getParameter('app.path.product_images') . '/barcode/';
    	$filename = $code.'.png';
    		
    	return $fs->exists($path . $filename);
    }
    
    public function generateBarcodeImage($code)
    {
    	$barcode = $this->get('hackzilla_barcode');
    	$barcode->setMode(Barcode::MODE_PNG);
    
    	$filename = $code.'.png';
    
    
    	$fs = new FileSystem();
    	$path = $this->container->getParameter('web_dir') . $this->container->getParameter('app.path.product_images') . '/barcode/';
    
    	if(!$fs->exists($path)) {
    		try {
    			$fs->mkdir($path, 0700);
    		} catch (IOExceptionInterface $e) {
    			echo "An error occurred while creating your directory at ".$e->getPath();
    		}
    	}
    
    	return $barcode->save($code, $path . $filename);
    }
}
