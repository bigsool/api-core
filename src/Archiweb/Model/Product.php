<?php

namespace Archiweb\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 */
class Product {

    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $duration;

    /**
     * @var string
     */
    private $bundleid;

    /**
     * @var string
     */
    private $name;

    /**
     * @var boolean
     */
    private $consumable;

    /**
     * @var float
     */
    private $price;

    /**
     * @var integer
     */
    private $weight;

    /**
     * @var boolean
     */
    private $available;

    /**
     * @var float
     */
    private $vat;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $purchasedProducts;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $productFunctionalities;

    /**
     * Constructor
     */
    public function __construct () {

        $this->purchasedProducts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->productFunctionalities = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId () {

        return $this->id;
    }

    /**
     * Get duration
     *
     * @return integer
     */
    public function getDuration () {

        return $this->duration;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     *
     * @return Product
     */
    public function setDuration ($duration) {

        $this->duration = $duration;

        return $this;
    }

    /**
     * Get bundleid
     *
     * @return string
     */
    public function getBundleid () {

        return $this->bundleid;
    }

    /**
     * Set bundleid
     *
     * @param string $bundleid
     *
     * @return Product
     */
    public function setBundleid ($bundleid) {

        $this->bundleid = $bundleid;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName () {

        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Product
     */
    public function setName ($name) {

        $this->name = $name;

        return $this;
    }

    /**
     * Get consumable
     *
     * @return boolean
     */
    public function getConsumable () {

        return $this->consumable;
    }

    /**
     * Set consumable
     *
     * @param boolean $consumable
     *
     * @return Product
     */
    public function setConsumable ($consumable) {

        $this->consumable = $consumable;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice () {

        return $this->price;
    }

    /**
     * Set price
     *
     * @param float $price
     *
     * @return Product
     */
    public function setPrice ($price) {

        $this->price = $price;

        return $this;
    }

    /**
     * Get weight
     *
     * @return integer
     */
    public function getWeight () {

        return $this->weight;
    }

    /**
     * Set weight
     *
     * @param integer $weight
     *
     * @return Product
     */
    public function setWeight ($weight) {

        $this->weight = $weight;

        return $this;
    }

    /**
     * Get available
     *
     * @return boolean
     */
    public function getAvailable () {

        return $this->available;
    }

    /**
     * Set available
     *
     * @param boolean $available
     *
     * @return Product
     */
    public function setAvailable ($available) {

        $this->available = $available;

        return $this;
    }

    /**
     * Get vat
     *
     * @return float
     */
    public function getVat () {

        return $this->vat;
    }

    /**
     * Set vat
     *
     * @param float $vat
     *
     * @return Product
     */
    public function setVat ($vat) {

        $this->vat = $vat;

        return $this;
    }

    /**
     * Add purchasedProducts
     *
     * @param \Archiweb\Model\PurchasedProduct $purchasedProducts
     *
     * @return Product
     */
    public function addPurchasedProduct (\Archiweb\Model\PurchasedProduct $purchasedProducts) {

        $this->purchasedProducts[] = $purchasedProducts;

        return $this;
    }

    /**
     * Remove purchasedProducts
     *
     * @param \Archiweb\Model\PurchasedProduct $purchasedProducts
     */
    public function removePurchasedProduct (\Archiweb\Model\PurchasedProduct $purchasedProducts) {

        $this->purchasedProducts->removeElement($purchasedProducts);
    }

    /**
     * Get purchasedProducts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPurchasedProducts () {

        return $this->purchasedProducts;
    }

    /**
     * Add productFunctionalities
     *
     * @param \Archiweb\Model\ProductFunctionality $productFunctionalities
     *
     * @return Product
     */
    public function addProductFunctionality (\Archiweb\Model\ProductFunctionality $productFunctionalities) {

        $this->productFunctionalities[] = $productFunctionalities;

        return $this;
    }

    /**
     * Remove productFunctionalities
     *
     * @param \Archiweb\Model\ProductFunctionality $productFunctionalities
     */
    public function removeProductFunctionality (\Archiweb\Model\ProductFunctionality $productFunctionalities) {

        $this->productFunctionalities->removeElement($productFunctionalities);
    }

    /**
     * Get productFunctionalities
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductFunctionalities () {

        return $this->productFunctionalities;
    }
}
