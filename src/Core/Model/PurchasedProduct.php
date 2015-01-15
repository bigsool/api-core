<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * PurchasedProduct
 *
 * @ORM\Table(name="purchasedProduct", indexes={@ORM\Index(name="transaction_id", columns={"transaction_id"})})
 * @ORM\Entity
 */
class PurchasedProduct {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var float
     *
     * @ORM\Column(name="real_price", type="float", precision=10, scale=0, nullable=true)
     */
    private $realPrice;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     */
    private $quantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="durationFactor", type="integer", nullable=false)
     */
    private $durationfactor;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endDate", type="date", nullable=true)
     */
    private $enddate;

    /**
     * @var \Core\Model\Transaction
     *
     * @ORM\ManyToOne(targetEntity="Core\Model\Transaction", inversedBy="purchasedProducts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="transaction_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $transaction;

    /**
     * @var \Core\Model\Product
     *
     * @ORM\ManyToOne(targetEntity="Core\Model\Product", inversedBy="purchasedProducts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="product_id", referencedColumnName="id", nullable=false)
     * })
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
