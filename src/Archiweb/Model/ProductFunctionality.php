<?php

namespace Archiweb\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductFunctionality
 */
class ProductFunctionality {

    /**
     * @var integer
     */
    private $quantity;

    /**
     * @var \Archiweb\Model\Functionality
     */
    private $functionality;

    /**
     * @var \Archiweb\Model\Product
     */
    private $product;

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity () {

        return $this->quantity;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return ProductFunctionality
     */
    public function setQuantity ($quantity) {

        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get functionality
     *
     * @return \Archiweb\Model\Functionality
     */
    public function getFunctionality () {

        return $this->functionality;
    }

    /**
     * Set functionality
     *
     * @param \Archiweb\Model\Functionality $functionality
     *
     * @return ProductFunctionality
     */
    public function setFunctionality (\Archiweb\Model\Functionality $functionality) {

        $this->functionality = $functionality;

        return $this;
    }

    /**
     * Get product
     *
     * @return \Archiweb\Model\Product
     */
    public function getProduct () {

        return $this->product;
    }

    /**
     * Set product
     *
     * @param \Archiweb\Model\Product $product
     *
     * @return ProductFunctionality
     */
    public function setProduct (\Archiweb\Model\Product $product) {

        $this->product = $product;

        return $this;
    }
}
