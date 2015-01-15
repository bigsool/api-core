<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Transaction
 *
 * @ORM\Table(name="transaction")
 * @ORM\Entity
 */
class Transaction
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
     * @var \DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    private $timestamp;

    /**
     * @var string
     *
     * @ORM\Column(name="invoicingStatus", type="string", length=255, nullable=false)
     */
    private $invoicingstatus;

    /**
     * @var string
     *
     * @ORM\Column(name="paymentStatus", type="string", length=255, nullable=false)
     */
    private $paymentstatus;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=255, nullable=true)
     */
    private $comment;


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
}
