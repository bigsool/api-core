<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * ShippingAddress
 */
class ShippingAddress {

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $address;

    /**
     * @var string
     */
    private $zipcode;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $state;

    /**
     * @var string
     */
    private $country;

    /**
     * @var \Core\Model\Transaction
     */
    private $transaction;

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
     * @return ShippingAddress
     */
    public function setName ($name) {

        $this->name = $name;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress () {

        return $this->address;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return ShippingAddress
     */
    public function setAddress ($address) {

        $this->address = $address;

        return $this;
    }

    /**
     * Get zipcode
     *
     * @return string
     */
    public function getZipcode () {

        return $this->zipcode;
    }

    /**
     * Set zipcode
     *
     * @param string $zipcode
     *
     * @return ShippingAddress
     */
    public function setZipcode ($zipcode) {

        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity () {

        return $this->city;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return ShippingAddress
     */
    public function setCity ($city) {

        $this->city = $city;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState () {

        return $this->state;
    }

    /**
     * Set state
     *
     * @param string $state
     *
     * @return ShippingAddress
     */
    public function setState ($state) {

        $this->state = $state;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry () {

        return $this->country;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return ShippingAddress
     */
    public function setCountry ($country) {

        $this->country = $country;

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
     * @return ShippingAddress
     */
    public function setTransaction (\Core\Model\Transaction $transaction) {

        $this->transaction = $transaction;

        return $this;
    }
}