<?php

namespace Core\Model;

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
     * @var \Core\Model\Functionality
     */
    private $functionality;

    /**
     * @var \Core\Model\Product
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
     * @return \Core\Model\Functionality
     */
    public function getFunctionality () {

        return $this->functionality;
    }

    /**
     * Set functionality
     *
     * @param \Core\Model\Functionality $functionality
     *
     * @return ProductFunctionality
     */
    public function setFunctionality (\Core\Model\Functionality $functionality) {

        $this->functionality = $functionality;

        return $this;
    }

    /**
     * Get product
     *
     * @return \Core\Model\Product
     */
    public function getProduct () {

        return $this->product;
    }

    /**
     * Set product
     *
     * @param \Core\Model\Product $product
     *
     * @return ProductFunctionality
     */
    public function setProduct (\Core\Model\Product $product) {

        $this->product = $product;

        return $this;
    }
}
