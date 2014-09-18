<?php

namespace Archiweb\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payment
 */
class Payment {

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $receiptData;

    /**
     * @var float
     */
    private $amount;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var \DateTime
     */
    private $timestamp;

    /**
     * @var string
     */
    private $txnId;

    /**
     * @var string
     */
    private $externalid;

    /**
     * @var float
     */
    private $vat;

    /**
     * @var float
     */
    private $shipping;

    /**
     * @var \Archiweb\Model\Transaction
     */
    private $transaction;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId () {

        return $this->id;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType () {

        return $this->type;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Payment
     */
    public function setType ($type) {

        $this->type = $type;

        return $this;
    }

    /**
     * Get receiptData
     *
     * @return string
     */
    public function getReceiptData () {

        return $this->receiptData;
    }

    /**
     * Set receiptData
     *
     * @param string $receiptData
     *
     * @return Payment
     */
    public function setReceiptData ($receiptData) {

        $this->receiptData = $receiptData;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount () {

        return $this->amount;
    }

    /**
     * Set amount
     *
     * @param float $amount
     *
     * @return Payment
     */
    public function setAmount ($amount) {

        $this->amount = $amount;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency () {

        return $this->currency;
    }

    /**
     * Set currency
     *
     * @param string $currency
     *
     * @return Payment
     */
    public function setCurrency ($currency) {

        $this->currency = $currency;

        return $this;
    }

    /**
     * Get timestamp
     *
     * @return \DateTime
     */
    public function getTimestamp () {

        return $this->timestamp;
    }

    /**
     * Set timestamp
     *
     * @param \DateTime $timestamp
     *
     * @return Payment
     */
    public function setTimestamp ($timestamp) {

        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get txnId
     *
     * @return string
     */
    public function getTxnId () {

        return $this->txnId;
    }

    /**
     * Set txnId
     *
     * @param string $txnId
     *
     * @return Payment
     */
    public function setTxnId ($txnId) {

        $this->txnId = $txnId;

        return $this;
    }

    /**
     * Get externalid
     *
     * @return string
     */
    public function getExternalid () {

        return $this->externalid;
    }

    /**
     * Set externalid
     *
     * @param string $externalid
     *
     * @return Payment
     */
    public function setExternalid ($externalid) {

        $this->externalid = $externalid;

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
     * @return Payment
     */
    public function setVat ($vat) {

        $this->vat = $vat;

        return $this;
    }

    /**
     * Get shipping
     *
     * @return float
     */
    public function getShipping () {

        return $this->shipping;
    }

    /**
     * Set shipping
     *
     * @param float $shipping
     *
     * @return Payment
     */
    public function setShipping ($shipping) {

        $this->shipping = $shipping;

        return $this;
    }

    /**
     * Get transaction
     *
     * @return \Archiweb\Model\Transaction
     */
    public function getTransaction () {

        return $this->transaction;
    }

    /**
     * Set transaction
     *
     * @param \Archiweb\Model\Transaction $transaction
     *
     * @return Payment
     */
    public function setTransaction (\Archiweb\Model\Transaction $transaction = NULL) {

        $this->transaction = $transaction;

        return $this;
    }
}
