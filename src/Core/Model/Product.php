<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="product", uniqueConstraints={@ORM\UniqueConstraint(name="productBundleId", columns={"bundleId"})})
 * @ORM\Entity
 */
class Product
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="duration", type="integer", nullable=true)
     */
    private $duration;

    /**
     * @var string
     *
     * @ORM\Column(name="bundleId", type="string", length=255, nullable=false)
     */
    private $bundleid;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="consumable", type="boolean", nullable=false)
     */
    private $consumable;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", precision=10, scale=0, nullable=false)
     */
    private $price;

    /**
     * @var integer
     *
     * @ORM\Column(name="weight", type="integer", nullable=false)
     */
    private $weight;

    /**
     * @var boolean
     *
     * @ORM\Column(name="available", type="boolean", nullable=false)
     */
    private $available;

    /**
     * @var float
     *
     * @ORM\Column(name="vat", type="float", precision=10, scale=0, nullable=false)
     */
    private $vat;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Core\Model\PurchasedProduct", mappedBy="product")
     */
    private $purchasedProducts;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Core\Model\ProductFunctionality", mappedBy="product")
     */
    private $productFunctionalities;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->purchasedProducts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->productFunctionalities = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     * @return Product
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return integer 
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set bundleid
     *
     * @param string $bundleid
     * @return Product
     */
    public function setBundleid($bundleid)
    {
        $this->bundleid = $bundleid;

        return $this;
    }

    /**
     * Get bundleid
     *
     * @return string 
     */
    public function getBundleid()
    {
        return $this->bundleid;
    }

    /**
     * Set name
     *
     * @param string $name
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
     * Set consumable
     *
     * @param boolean $consumable
     * @return Product
     */
    public function setConsumable($consumable)
    {
        $this->consumable = $consumable;

        return $this;
    }

    /**
     * Get consumable
     *
     * @return boolean 
     */
    public function getConsumable()
    {
        return $this->consumable;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set weight
     *
     * @param integer $weight
     * @return Product
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return integer 
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set available
     *
     * @param boolean $available
     * @return Product
     */
    public function setAvailable($available)
    {
        $this->available = $available;

        return $this;
    }

    /**
     * Get available
     *
     * @return boolean 
     */
    public function getAvailable()
    {
        return $this->available;
    }

    /**
     * Set vat
     *
     * @param float $vat
     * @return Product
     */
    public function setVat($vat)
    {
        $this->vat = $vat;

        return $this;
    }

    /**
     * Get vat
     *
     * @return float 
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * Add purchasedProducts
     *
     * @param \Core\Model\PurchasedProduct $purchasedProducts
     * @return Product
     */
    public function addPurchasedProduct(\Core\Model\PurchasedProduct $purchasedProducts)
    {
        $this->purchasedProducts[] = $purchasedProducts;

        return $this;
    }

    /**
     * Remove purchasedProducts
     *
     * @param \Core\Model\PurchasedProduct $purchasedProducts
     */
    public function removePurchasedProduct(\Core\Model\PurchasedProduct $purchasedProducts)
    {
        $this->purchasedProducts->removeElement($purchasedProducts);
    }

    /**
     * Get purchasedProducts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPurchasedProducts()
    {
        return $this->purchasedProducts;
    }

    /**
     * Add productFunctionalities
     *
     * @param \Core\Model\ProductFunctionality $productFunctionalities
     * @return Product
     */
    public function addProductFunctionality(\Core\Model\ProductFunctionality $productFunctionalities)
    {
        $this->productFunctionalities[] = $productFunctionalities;

        return $this;
    }

    /**
     * Remove productFunctionalities
     *
     * @param \Core\Model\ProductFunctionality $productFunctionalities
     */
    public function removeProductFunctionality(\Core\Model\ProductFunctionality $productFunctionalities)
    {
        $this->productFunctionalities->removeElement($productFunctionalities);
    }

    /**
     * Get productFunctionalities
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProductFunctionalities()
    {
        return $this->productFunctionalities;
    }
}
