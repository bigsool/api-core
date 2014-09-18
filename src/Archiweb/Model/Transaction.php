<?php

namespace Archiweb\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Transaction
 */
class Transaction
{
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
     * @var \Archiweb\Model\BillingAddress
     */
    private $billingAddress;

    /**
     * @var \Archiweb\Model\Invoice
     */
    private $invoice;

    /**
     * @var \Archiweb\Model\Payment
     */
    private $payment;

    /**
     * @var \Archiweb\Model\User
     */
    private $user;


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
     * Set timestamp
     *
     * @param \DateTime $timestamp
     * @return Transaction
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
     * Set invoicingstatus
     *
     * @param string $invoicingstatus
     * @return Transaction
     */
    public function setInvoicingstatus($invoicingstatus)
    {
        $this->invoicingstatus = $invoicingstatus;

        return $this;
    }

    /**
     * Get invoicingstatus
     *
     * @return string 
     */
    public function getInvoicingstatus()
    {
        return $this->invoicingstatus;
    }

    /**
     * Set paymentstatus
     *
     * @param string $paymentstatus
     * @return Transaction
     */
    public function setPaymentstatus($paymentstatus)
    {
        $this->paymentstatus = $paymentstatus;

        return $this;
    }

    /**
     * Get paymentstatus
     *
     * @return string 
     */
    public function getPaymentstatus()
    {
        return $this->paymentstatus;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return Transaction
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set billingAddress
     *
     * @param \Archiweb\Model\BillingAddress $billingAddress
     * @return Transaction
     */
    public function setBillingAddress(\Archiweb\Model\BillingAddress $billingAddress = null)
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    /**
     * Get billingAddress
     *
     * @return \Archiweb\Model\BillingAddress 
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * Set invoice
     *
     * @param \Archiweb\Model\Invoice $invoice
     * @return Transaction
     */
    public function setInvoice(\Archiweb\Model\Invoice $invoice = null)
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * Get invoice
     *
     * @return \Archiweb\Model\Invoice 
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * Set payment
     *
     * @param \Archiweb\Model\Payment $payment
     * @return Transaction
     */
    public function setPayment(\Archiweb\Model\Payment $payment = null)
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * Get payment
     *
     * @return \Archiweb\Model\Payment 
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * Set user
     *
     * @param \Archiweb\Model\User $user
     * @return Transaction
     */
    public function setUser(\Archiweb\Model\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Archiweb\Model\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
