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
    private $usedspace;

    /**
     * @var \DateTime
     */
    private $lastusedspaceupdate;

    /**
     * @var boolean
     */
    private $isoutofquota;

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
     * Get usedspace
     *
     * @return integer
     */
    public function getUsedspace () {

        return $this->usedspace;
    }

    /**
     * Set usedspace
     *
     * @param integer $usedspace
     *
     * @return Storage
     */
    public function setUsedspace ($usedspace) {

        $this->usedspace = $usedspace;

        return $this;
    }

    /**
     * Get lastusedspaceupdate
     *
     * @return \DateTime
     */
    public function getLastusedspaceupdate () {

        return $this->lastusedspaceupdate;
    }

    /**
     * Set lastusedspaceupdate
     *
     * @param \DateTime $lastusedspaceupdate
     *
     * @return Storage
     */
    public function setLastusedspaceupdate ($lastusedspaceupdate) {

        $this->lastusedspaceupdate = $lastusedspaceupdate;

        return $this;
    }

    /**
     * Get isoutofquota
     *
     * @return boolean
     */
    public function getIsoutofquota () {

        return $this->isoutofquota;
    }

    /**
     * Set isoutofquota
     *
     * @param boolean $isoutofquota
     *
     * @return Storage
     */
    public function setIsoutofquota ($isoutofquota) {

        $this->isoutofquota = $isoutofquota;

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
    public function setCompany (\Archiweb\Model\Company $company) {

        $this->company = $company;

        return $this;
    }
}
