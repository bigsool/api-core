<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * PurchasedProduct
 */
class PurchasedProduct {

    /**
     * @var integer
     */
    private $id;

    /**
     * @var float
     */
    private $realPrice;

    /**
     * @var integer
     */
    private $quantity;

    /**
     * @var integer
     */
    private $durationfactor;

    /**
     * @var \DateTime
     */
    private $enddate;

    /**
     * @var \Core\Model\Transaction
     */
    private $transaction;

    /**
     * @var \Core\Model\Product
     */
    private $product;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId () {

        return $this->id;
    }

    /**
     * Get realPrice
     *
     * @return float
     */
    public function getRealPrice () {

        return $this->realPrice;
    }

    /**
     * Set realPrice
     *
     * @param float $realPrice
     *
     * @return PurchasedProduct
     */
    public function setRealPrice ($realPrice) {

        $this->realPrice = $realPrice;

        return $this;
    }

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
     * @return PurchasedProduct
     */
    public function setQuantity ($quantity) {

        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get durationfactor
     *
     * @return integer
     */
    public function getDurationfactor () {

        return $this->durationfactor;
    }

    /**
     * Set durationfactor
     *
     * @param integer $durationfactor
     *
     * @return PurchasedProduct
     */
    public function setDurationfactor ($durationfactor) {

        $this->durationfactor = $durationfactor;

        return $this;
    }

    /**
     * Get enddate
     *
     * @return \DateTime
     */
    public function getEnddate () {

        return $this->enddate;
    }

    /**
     * Set enddate
     *
     * @param \DateTime $enddate
     *
     * @return PurchasedProduct
     */
    public function setEnddate ($enddate) {

        $this->enddate = $enddate;

        return $this;
    }

    /**
     * Get transaction
     *
     * @return \Core\Model\Transaction
     */
    public function getTransaction () {

        return $this->transaction;
    }

    /**
     * Set transaction
     *
     * @param \Core\Model\Transaction $transaction
     *
     * @return PurchasedProduct
     */
    public function setTransaction (\Core\Model\Transaction $transaction) {

        $this->transaction = $transaction;

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
     * @return PurchasedProduct
     */
    public function setProduct (\Core\Model\Product $product) {

        $this->product = $product;

        return $this;
    }
}
