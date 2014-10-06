<?php

namespace Archiweb\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Storage
 */
class Storage {

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $password;

    /**
     * @var integer
     */
    private $usedSpace;

    /**
     * @var \DateTime
     */
    private $lastUsedSpaceUpdate;

    /**
     * @var boolean
     */
    private $isOutOfQuota;

    /**
     * @var \Archiweb\Model\Company
     */
    private $company;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId () {

        return $this->id;
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
     * @return Storage
     */
    public function setUrl ($url) {

        $this->url = $url;

        return $this;
    }

    /**
     * Get login
     *
     * @return string
     */
    public function getLogin () {

        return $this->login;
    }

    /**
     * Set login
     *
     * @param string $login
     *
     * @return Storage
     */
    public function setLogin ($login) {

        $this->login = $login;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword () {

        return $this->password;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Storage
     */
    public function setPassword ($password) {

        $this->password = $password;

        return $this;
    }

    /**
     * Get usedSpace
     *
     * @return integer
     */
    public function getUsedSpace () {

        return $this->usedSpace;
    }

    /**
     * Set usedSpace
     *
     * @param integer $usedSpace
     *
     * @return Storage
     */
    public function setUsedSpace ($usedSpace) {

        $this->usedSpace = $usedSpace;

        return $this;
    }

    /**
     * Get lastUsedSpaceUpdate
     *
     * @return \DateTime
     */
    public function getLastUsedSpaceUpdate () {

        return $this->lastUsedSpaceUpdate;
    }

    /**
     * Set lastUsedSpaceUpdate
     *
     * @param \DateTime $lastUsedSpaceUpdate
     *
     * @return Storage
     */
    public function setLastUsedSpaceUpdate ($lastUsedSpaceUpdate) {

        $this->lastUsedSpaceUpdate = $lastUsedSpaceUpdate;

        return $this;
    }

    /**
     * Get isOutOfQuota
     *
     * @return boolean
     */
    public function getIsOutOfQuota () {

        return $this->isOutOfQuota;
    }

    /**
     * Set isOutOfQuota
     *
     * @param boolean $isOutOfQuota
     *
     * @return Storage
     */
    public function setIsOutOfQuota ($isOutOfQuota) {

        $this->isOutOfQuota = $isOutOfQuota;

        return $this;
    }

    /**
     * Get company
     *
     * @return \Archiweb\Model\Company
     */
    public function getCompany () {

        return $this->company;
    }

    /**
     * Set company
     *
     * @param \Archiweb\Model\Company $company
     *
     * @return Storage
     */
    public function setCompany (\Archiweb\Model\Company $company = NULL) {

        $this->company = $company;

        return $this;
    }
}
