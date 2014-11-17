<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompanyMyISAM
 */
class CompanyMyISAM {

    /**
     * @var integer
     */
    private $id;

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
     * @var string
     */
    private $tel;

    /**
     * @var string
     */
    private $fax;

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
     * @return CompanyMyISAM
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
     * @return CompanyMyISAM
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
     * @return CompanyMyISAM
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
     * @return CompanyMyISAM
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
     * @return CompanyMyISAM
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
     * @return CompanyMyISAM
     */
    public function setCountry ($country) {

        $this->country = $country;

        return $this;
    }

    /**
     * Get tel
     *
     * @return string
     */
    public function getTel () {

        return $this->tel;
    }

    /**
     * Set tel
     *
     * @param string $tel
     *
     * @return CompanyMyISAM
     */
    public function setTel ($tel) {

        $this->tel = $tel;

        return $this;
    }

    /**
     * Get fax
     *
     * @return string
     */
    public function getFax () {

        return $this->fax;
    }

    /**
     * Set fax
     *
     * @param string $fax
     *
     * @return CompanyMyISAM
     */
    public function setFax ($fax) {

        $this->fax = $fax;

        return $this;
    }
}
