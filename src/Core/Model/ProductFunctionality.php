<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductFunctionality
 *
 * @ORM\Table(name="productFunctionality")
 * @ORM\Entity
 */
class ProductFunctionality {

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     */
    private $quantity;

    /**
     * @var \Core\Model\Functionality
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Core\Model\Functionality", inversedBy="productFunctionalities")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="functionality_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $functionality;

    /**
     * @var \Core\Model\Product
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Core\Model\Product", inversedBy="productFunctionalities")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="product_id", referencedColumnName="id", nullable=false)
     * })
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
