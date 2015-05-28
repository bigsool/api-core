<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * TestStorage
 *
 * @ORM\Table(name="storage")
 * @ORM\Entity
 */
class TestStorage {

    /**
     * @var \Core\Context\FindQueryContext
     */
    protected $findQueryContext;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=false)
     */
    protected $url;

    /**
     * @var string
     *
     * @ORM\Column(name="login", type="string", length=255, nullable=false)
     */
    protected $login;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    protected $password;

    /**
     * @var integer
     *
     * @ORM\Column(name="usedSpace", type="bigint", nullable=false)
     */
    protected $usedSpace = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastUsedSpaceUpdate", type="datetime", nullable=false)
     */
    protected $lastUsedSpaceUpdate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isOutOfQuota", type="boolean", nullable=false)
     */
    protected $isOutOfQuota = '0';

    /**
     * @var \Core\Model\TestCompany
     *
     * @ORM\OneToOne(targetEntity="Core\Model\TestCompany", mappedBy="storage", cascade={"persist"})
     */
    protected $company;

    /**
     * @var int
     */
    protected $companyRestrictedId;

    /**
     * @var bool
     */
    protected $isCompanyFaulted = true;

    /**
     * @var \Core\Model\TestUser
     *
     * @ORM\OneToOne(targetEntity="Core\Model\TestUser", mappedBy="storage", cascade={"persist"})
     */
    protected $user;

    /**
     * @var int
     */
    protected $userRestrictedId;

    /**
     * @var bool
     */
    protected $isUserFaulted = true;

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
     * @return TestStorage
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
     * @return TestStorage
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
     * @return TestStorage
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
     * @return TestStorage
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
     * @return TestStorage
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
     * @return TestStorage
     */
    public function setIsOutOfQuota ($isOutOfQuota) {

        $this->isOutOfQuota = $isOutOfQuota;

        return $this;
    }

    /**
     * Get company
     *
     * @return \Core\Model\TestCompany
     */
    public function getCompany () {

        if (!$this->companyRestrictedId && $this->findQueryContext) {
            $faultedVar = "is" . ucfirst("company") . "Faulted";
            if (!$this->$faultedVar) {
                return NULL;
            }
            $this->$faultedVar = false; // TODO : set to false in the hydrator too
            $reqCtx = $this->findQueryContext->getRequestContext()->copyWithoutRequestedFields();
            $reqCtx->setReturnedFields([new \Core\Field\RelativeField("id"), new \Core\Field\RelativeField("company")]);
            $qryContext = new \Core\Context\FindQueryContext("TestStorage", $reqCtx);
            $qryContext->addFilter(new \Core\Filter\StringFilter("TestStorage", "", "id = :id"));
            $qryContext->setParam("id", $this->getId());
            $qryContext->findAll();
        }

        return $this->company && $this->company->getId() == $this->companyRestrictedId ? $this->company : NULL;
    }

    /**
     * Set company
     *
     * @param \Core\Model\TestCompany $company
     *
     * @return TestStorage
     */
    public function setCompany (\Core\Model\TestCompany $company = NULL) {

        $this->company = $company;
        $this->companyRestrictedId = $company ? $company->getId() : NULL;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId () {

        return $this->id;
    }

    /**
     * Get company
     *
     * @return \Core\Model\TestCompany
     */
    public function getUnrestrictedCompany () {

        return $this->company;
    }

    /**
     * Get user
     *
     * @return \Core\Model\TestUser
     */
    public function getUser () {

        if (!$this->userRestrictedId && $this->findQueryContext) {
            $faultedVar = "is" . ucfirst("user") . "Faulted";
            if (!$this->$faultedVar) {
                return NULL;
            }
            $this->$faultedVar = false; // TODO : set to false in the hydrator too
            $reqCtx = $this->findQueryContext->getRequestContext()->copyWithoutRequestedFields();
            $reqCtx->setReturnedFields([new \Core\Field\RelativeField("id"), new \Core\Field\RelativeField("user")]);
            $qryContext = new \Core\Context\FindQueryContext("TestStorage", $reqCtx);
            $qryContext->addFilter(new \Core\Filter\StringFilter("TestStorage", "", "id = :id"));
            $qryContext->setParam("id", $this->getId());
            $qryContext->findAll();
        }

        return $this->user && $this->user->getId() == $this->userRestrictedId ? $this->user : NULL;
    }

    /**
     * Set user
     *
     * @param \Core\Model\TestUser $user
     *
     * @return TestStorage
     */
    public function setUser (\Core\Model\TestUser $user = NULL) {

        $this->user = $user;
        $this->userRestrictedId = $user ? $user->getId() : NULL;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Core\Model\TestUser
     */
    public function getUnrestrictedUser () {

        return $this->user;
    }
}

