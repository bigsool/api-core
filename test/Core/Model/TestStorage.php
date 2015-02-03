<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * TestStorage
 *
 * @ORM\Table(name="storage")
 * @ORM\Entity
 */
class TestStorage
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
     * @var \Core\Model\TestCompany
     *
     * @ORM\OneToOne(targetEntity="Core\Model\TestCompany", inversedBy="storage", cascade={"persist"})
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
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return TestStorage
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set login
     *
     * @param string $login
     * @return TestStorage
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get login
     *
     * @return string 
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return TestStorage
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set usedSpace
     *
     * @param integer $usedSpace
     * @return TestStorage
     */
    public function setUsedSpace($usedSpace)
    {
        $this->usedSpace = $usedSpace;

        return $this;
    }

    /**
     * Get usedSpace
     *
     * @return integer 
     */
    public function getUsedSpace()
    {
        return $this->usedSpace;
    }

    /**
     * Set lastUsedSpaceUpdate
     *
     * @param \DateTime $lastUsedSpaceUpdate
     * @return TestStorage
     */
    public function setLastUsedSpaceUpdate($lastUsedSpaceUpdate)
    {
        $this->lastUsedSpaceUpdate = $lastUsedSpaceUpdate;

        return $this;
    }

    /**
     * Get lastUsedSpaceUpdate
     *
     * @return \DateTime 
     */
    public function getLastUsedSpaceUpdate()
    {
        return $this->lastUsedSpaceUpdate;
    }

    /**
     * Set isOutOfQuota
     *
     * @param boolean $isOutOfQuota
     * @return TestStorage
     */
    public function setIsOutOfQuota($isOutOfQuota)
    {
        $this->isOutOfQuota = $isOutOfQuota;

        return $this;
    }

    /**
     * Get isOutOfQuota
     *
     * @return boolean 
     */
    public function getIsOutOfQuota()
    {
        return $this->isOutOfQuota;
    }

    /**
     * Set company
     *
     * @param \Core\Model\TestCompany $company
     * @return TestStorage
     */
    public function setCompany(\Core\Model\TestCompany $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return \Core\Model\TestCompany 
     */
    public function getCompany()
    {
        return $this->company;
    }
}