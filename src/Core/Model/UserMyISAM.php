<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserMyISAM
 */
class UserMyISAM {

    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $companyId;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $firstname;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId () {

        return $this->id;
    }

    /**
     * Get companyId
     *
     * @return integer
     */
    public function getCompanyId () {

        return $this->companyId;
    }

    /**
     * Set companyId
     *
     * @param integer $companyId
     *
     * @return UserMyISAM
     */
    public function setCompanyId ($companyId) {

        $this->companyId = $companyId;

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
     * @return UserMyISAM
     */
    public function setEmail ($email) {

        $this->email = $email;

        return $this;
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
     * @return UserMyISAM
     */
    public function setName ($name) {

        $this->name = $name;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname () {

        return $this->firstname;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return UserMyISAM
     */
    public function setFirstname ($firstname) {

        $this->firstname = $firstname;

        return $this;
    }
}
