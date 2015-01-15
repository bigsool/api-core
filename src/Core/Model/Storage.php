<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Storage
 *
 * @ORM\Table(name="storage")
 * @ORM\Entity
 */
class Storage {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=false)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="login", type="string", length=255, nullable=false)
     */
    private $login;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    private $password;

    /**
     * @var integer
     *
     * @ORM\Column(name="usedSpace", type="bigint", nullable=false)
     */
    private $usedSpace;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastUsedSpaceUpdate", type="datetime", nullable=false)
     */
    private $lastUsedSpaceUpdate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isOutOfQuota", type="boolean", nullable=false)
     */
    private $isOutOfQuota;

    /**
     * @var \Core\Model\Company
     *
     * @ORM\OneToOne(targetEntity="Core\Model\Company", inversedBy="storage", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="company_id", referencedColumnName="id", unique=true, nullable=true)
     * })
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
     * @return \Core\Model\Company
     */
    public function getCompany () {

        return $this->company;
    }

    /**
     * Set company
     *
     * @param \Core\Model\Company $company
     *
     * @return Storage
     */
    public function setCompany (\Core\Model\Company $company = NULL) {

        $this->company = $company;

        return $this;
    }
}
