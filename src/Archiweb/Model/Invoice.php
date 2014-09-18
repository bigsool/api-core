<?php

namespace Archiweb\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Invoice
 */
class Invoice {

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var string
     */
    private $url;

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
     * Get name
     *
     * @return string
     */
    public function getName () {

        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Invoice
     */
    public function setName ($name) {

        $this->name = $name;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate () {

        return $this->date;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Invoice
     */
    public function setDate ($date) {

        $this->date = $date;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl () {

        return $this->url;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Invoice
     */
    public function setUrl ($url) {

        $this->url = $url;

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
     * @return Invoice
     */
    public function setTransaction (\Archiweb\Model\Transaction $transaction = NULL) {

        $this->transaction = $transaction;

        return $this;
    }
}
