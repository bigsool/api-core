<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payment
 *
 * @ORM\Table(name="payment")
 * @ORM\Entity
 */
class Payment
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
     * @var string
     *
     * @ORM\Column(name="type", type="string", nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="receipt_data", type="text", nullable=false)
     */
    private $receiptData;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float", precision=10, scale=0, nullable=false)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=3, nullable=false)
     */
    private $currency;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    private $timestamp;

    /**
     * @var string
     *
     * @ORM\Column(name="txn_id", type="string", length=50, nullable=true)
     */
    private $txnId;

    /**
     * @var string
     *
     * @ORM\Column(name="externalId", type="string", length=255, nullable=true)
     */
    private $externalid;

    /**
     * @var float
     *
     * @ORM\Column(name="vat", type="float", precision=10, scale=0, nullable=false)
     */
    private $vat;

    /**
     * @var float
     *
     * @ORM\Column(name="shipping", type="float", precision=10, scale=0, nullable=false)
     */
    private $shipping;


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
     * Set type
     *
     * @param string $type
     * @return Payment
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set receiptData
     *
     * @param string $receiptData
     * @return Payment
     */
    public function setReceiptData($receiptData)
    {
        $this->receiptData = $receiptData;

        return $this;
    }

    /**
     * Get receiptData
     *
     * @return string 
     */
    public function getReceiptData()
    {
        return $this->receiptData;
    }

    /**
     * Set amount
     *
     * @param float $amount
     * @return Payment
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set currency
     *
     * @param string $currency
     * @return Payment
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string 
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set timestamp
     *
     * @param \DateTime $timestamp
     * @return Payment
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get timestamp
     *
     * @return \DateTime 
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Set txnId
     *
     * @param string $txnId
     * @return Payment
     */
    public function setTxnId($txnId)
    {
        $this->txnId = $txnId;

        return $this;
    }

    /**
     * Get txnId
     *
     * @return string 
     */
    public function getTxnId()
    {
        return $this->txnId;
    }

    /**
     * Set externalid
     *
     * @param string $externalid
     * @return Payment
     */
    public function setExternalid($externalid)
    {
        $this->externalid = $externalid;

        return $this;
    }

    /**
     * Get externalid
     *
     * @return string 
     */
    public function getExternalid()
    {
        return $this->externalid;
    }

    /**
     * Set vat
     *
     * @param float $vat
     * @return Payment
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
     * Set shipping
     *
     * @param float $shipping
     * @return Payment
     */
    public function setShipping($shipping)
    {
        $this->shipping = $shipping;

        return $this;
    }

    /**
     * Get shipping
     *
     * @return float 
     */
    public function getShipping()
    {
        return $this->shipping;
    }
}
