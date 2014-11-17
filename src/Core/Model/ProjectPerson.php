<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectPerson
 */
class ProjectPerson {

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
    private $phone;

    /**
     * @var string
     */
    private $mobile;

    /**
     * @var string
     */
    private $fax;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $company;

    /**
     * @var string
     */
    private $abbreviation;

    /**
     * @var \Core\Model\User
     */
    private $creator;

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
     * @return ProjectPerson
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
     * @return ProjectPerson
     */
    public function setAddress ($address) {

        $this->address = $address;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone () {

        return $this->phone;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return ProjectPerson
     */
    public function setPhone ($phone) {

        $this->phone = $phone;

        return $this;
    }

    /**
     * Get mobile
     *
     * @return string
     */
    public function getMobile () {

        return $this->mobile;
    }

    /**
     * Set mobile
     *
     * @param string $mobile
     *
     * @return ProjectPerson
     */
    public function setMobile ($mobile) {

        $this->mobile = $mobile;

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
     * @return ProjectPerson
     */
    public function setFax ($fax) {

        $this->fax = $fax;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail () {

        return $this->email;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return ProjectPerson
     */
    public function setEmail ($email) {

        $this->email = $email;

        return $this;
    }

    /**
     * Get company
     *
     * @return string
     */
    public function getCompany () {

        return $this->company;
    }

    /**
     * Set company
     *
     * @param string $company
     *
     * @return ProjectPerson
     */
    public function setCompany ($company) {

        $this->company = $company;

        return $this;
    }

    /**
     * Get abbreviation
     *
     * @return string
     */
    public function getAbbreviation () {

        return $this->abbreviation;
    }

    /**
     * Set abbreviation
     *
     * @param string $abbreviation
     *
     * @return ProjectPerson
     */
    public function setAbbreviation ($abbreviation) {

        $this->abbreviation = $abbreviation;

        return $this;
    }

    /**
     * Get creator
     *
     * @return \Core\Model\User
     */
    public function getCreator () {

        return $this->creator;
    }

    /**
     * Set creator
     *
     * @param \Core\Model\User $creator
     *
     * @return ProjectPerson
     */
    public function setCreator (\Core\Model\User $creator) {

        $this->creator = $creator;

        return $this;
    }
}
