<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Transaction
 */
class Transaction {

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $timestamp;

    /**
     * @var string
     */
    private $invoicingstatus;

    /**
     * @var string
     */
    private $paymentstatus;

    /**
     * @var string
     */
    private $comment;

    /**
     * @var \Core\Model\BillingAddress
     */
    private $billingAddress;

    /**
     * @var \Core\Model\Invoice
     */
    private $invoice;

    /**
     * @var \Core\Model\Payment
     */
    private $payment;

    /**
     * @var \Core\Model\User
     */
    private $user;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId () {

        return $this->id;
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
     * @return Transaction
     */
    public function setTimestamp ($timestamp) {

        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get invoicingstatus
     *
     * @return string
     */
    public function getInvoicingstatus () {

        return $this->invoicingstatus;
    }

    /**
     * Set invoicingstatus
     *
     * @param string $invoicingstatus
     *
     * @return Transaction
     */
    public function setInvoicingstatus ($invoicingstatus) {

        $this->invoicingstatus = $invoicingstatus;

        return $this;
    }

    /**
     * Get paymentstatus
     *
     * @return string
     */
    public function getPaymentstatus () {

        return $this->paymentstatus;
    }

    /**
     * Set paymentstatus
     *
     * @param string $paymentstatus
     *
     * @return Transaction
     */
    public function setPaymentstatus ($paymentstatus) {

        $this->paymentstatus = $paymentstatus;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment () {

        return $this->comment;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Transaction
     */
    public function setComment ($comment) {

        $this->comment = $comment;

        return $this;
    }

    /**
     * Get billingAddress
     *
     * @return \Core\Model\BillingAddress
     */
    public function getBillingAddress () {

        return $this->billingAddress;
    }

    /**
     * Set billingAddress
     *
     * @param \Core\Model\BillingAddress $billingAddress
     *
     * @return Transaction
     */
    public function setBillingAddress (\Core\Model\BillingAddress $billingAddress = NULL) {

        $this->billingAddress = $billingAddress;

        return $this;
    }

    /**
     * Get invoice
     *
     * @return \Core\Model\Invoice
     */
    public function getInvoice () {

        return $this->invoice;
    }

    /**
     * Set invoice
     *
     * @param \Core\Model\Invoice $invoice
     *
     * @return Transaction
     */
    public function setInvoice (\Core\Model\Invoice $invoice = NULL) {

        $this->invoice = $invoice;

        return $this;
    }

    /**
     * Get payment
     *
     * @return \Core\Model\Payment
     */
    public function getPayment () {

        return $this->payment;
    }

    /**
     * Set payment
     *
     * @param \Core\Model\Payment $payment
     *
     * @return Transaction
     */
    public function setPayment (\Core\Model\Payment $payment = NULL) {

        $this->payment = $payment;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Core\Model\User
     */
    public function getUser () {

        return $this->user;
    }

    /**
     * Set user
     *
     * @param \Core\Model\User $user
     *
     * @return Transaction
     */
    public function setUser (\Core\Model\User $user) {

        $this->user = $user;

        return $this;
    }
}
