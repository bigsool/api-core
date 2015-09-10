<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * TestUser
 *
 * @ORM\Table(name="user")
 * @ORM\Entity
 */
class TestUser
{
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
     * @ORM\Column(name="confirmationKey", type="string", length=255, nullable=true)
     */
    protected $confirmationKey;

    /**
     * @var string
     *
     * @ORM\Column(name="lastName", type="string", length=255, nullable=true)
     */
    protected $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="firstName", type="string", length=255, nullable=true)
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="lang", type="string", length=255, nullable=false)
     */
    protected $lang;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creationDate", type="datetime", nullable=false)
     */
    protected $creationDate;

    /**
     * @var \Core\Model\TestCompany
     *
     * @ORM\OneToOne(targetEntity="Core\Model\TestCompany", mappedBy="owner", cascade={"persist"})
     */
    protected $ownedCompany;

    /**
     * @var int
     */
    protected $ownedCompanyRestrictedId;

    /**
     * @var bool
     */
    protected $isOwnedCompanyFaulted = true;

    /**
     * @var \Core\Model\TestCredential
     *
     * @ORM\OneToOne(targetEntity="Core\Model\TestCredential", inversedBy="user", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="credential_id", referencedColumnName="id", unique=true, nullable=true)
     * })
     */
    protected $credential;

    /**
     * @var int
     */
    protected $credentialRestrictedId;

    /**
     * @var bool
     */
    protected $isCredentialFaulted = true;

    /**
     * @var \Core\Model\TestCompany
     *
     * @ORM\ManyToOne(targetEntity="Core\Model\TestCompany", inversedBy="users", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=true)
     * })
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
     * Constructor
     * @internal You don't have to explicitly call the constructor of this entity. Use the ModuleEntity instead.
     */
    public function __construct()
    {
    }

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
     * Set confirmationKey
     *
     * @param string $confirmationKey
     *
     * @return TestUser
     */
    public function setConfirmationKey($confirmationKey)
    {
        $this->confirmationKey = $confirmationKey;

        return $this;
    }

    /**
     * Get confirmationKey
     *
     * @return string
     */
    public function getConfirmationKey()
    {
        return $this->confirmationKey;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return TestUser
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return TestUser
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lang
     *
     * @param string $lang
     *
     * @return TestUser
     */
    public function setLang($lang)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * Get lang
     *
     * @return string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return TestUser
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set ownedCompany
     *
     * @param \Core\Model\TestCompany $ownedCompany
     *
     * @return TestUser
     */
    public function setOwnedCompany(\Core\Model\TestCompany $ownedCompany = null)
    {
        $this->ownedCompany = $ownedCompany;
        $this->ownedCompanyRestrictedId = $ownedCompany ? $ownedCompany->getId() : NULL;

        return $this;
    }

    /**
     * Get ownedCompany
     *
     * @return \Core\Model\TestCompany
     */
    public function getOwnedCompany()
    {

        $reqCtx = $this->findQueryContext ? $this->findQueryContext->getRequestContext() : \Core\Context\ApplicationContext::getInstance()->getInitialRequestContext();

        if (!$this->ownedCompanyRestrictedId) {
            $faultedVar = "is".ucfirst("ownedCompany")."Faulted";
            if (!$this->$faultedVar) {
                return NULL;
            }
            $this->$faultedVar = false; // TODO : set to false in the hydrator too
            $reqCtx = $reqCtx->copyWithoutRequestedFields();
            $qryContext = new \Core\Context\FindQueryContext("TestCompany", $reqCtx);
            $qryContext->addFields("id","owner");
            $qryContext->addFilter(new \Core\Filter\StringFilter("TestCompany","","owner.id = :id"), $this->getId());
            $qryContext->findAll();
            // this query will hydrate TestUser and TestCompany
            // RestrictedObjectHydrator will automatically hydrate ownedCompanyRestrictedId
            // Since Doctrine shares model instances, ownedCompanyRestrictedId will be automatically available
        }

        return $this->ownedCompany && $this->ownedCompany->getId() == $this->ownedCompanyRestrictedId ? $this->ownedCompany : NULL;
    }

    /**
     * Get ownedCompany
     *
     * @return \Core\Model\TestCompany
     */
    public function getUnrestrictedOwnedCompany()
    {
        return $this->ownedCompany;
    }

    /**
     * Set credential
     *
     * @param \Core\Model\TestCredential $credential
     *
     * @return TestUser
     */
    public function setCredential(\Core\Model\TestCredential $credential = null)
    {
        $this->credential = $credential;
        $this->credentialRestrictedId = $credential ? $credential->getId() : NULL;

        return $this;
    }

    /**
     * Get credential
     *
     * @return \Core\Model\TestCredential
     */
    public function getCredential()
    {

        $reqCtx = $this->findQueryContext ? $this->findQueryContext->getRequestContext() : \Core\Context\ApplicationContext::getInstance()->getInitialRequestContext();

        if (!$this->credentialRestrictedId) {
            $faultedVar = "is".ucfirst("credential")."Faulted";
            if (!$this->$faultedVar) {
                return NULL;
            }
            $this->$faultedVar = false; // TODO : set to false in the hydrator too
            $reqCtx = $reqCtx->copyWithoutRequestedFields();
            $qryContext = new \Core\Context\FindQueryContext("TestCredential", $reqCtx);
            $qryContext->addFields("id","user");
            $qryContext->addFilter(new \Core\Filter\StringFilter("TestCredential","","user.id = :id"), $this->getId());
            $qryContext->findAll();
            // this query will hydrate TestUser and TestCredential
            // RestrictedObjectHydrator will automatically hydrate credentialRestrictedId
            // Since Doctrine shares model instances, credentialRestrictedId will be automatically available
        }

        return $this->credential && $this->credential->getId() == $this->credentialRestrictedId ? $this->credential : NULL;
    }

    /**
     * Get credential
     *
     * @return \Core\Model\TestCredential
     */
    public function getUnrestrictedCredential()
    {
        return $this->credential;
    }

    /**
     * Set company
     *
     * @param \Core\Model\TestCompany $company
     *
     * @return TestUser
     */
    public function setCompany(\Core\Model\TestCompany $company = null)
    {
        $this->company = $company;
        $this->companyRestrictedId = $company ? $company->getId() : NULL;

        return $this;
    }

    /**
     * Get company
     *
     * @return \Core\Model\TestCompany
     */
    public function getCompany()
    {

        $reqCtx = $this->findQueryContext ? $this->findQueryContext->getRequestContext() : \Core\Context\ApplicationContext::getInstance()->getInitialRequestContext();

        if (!$this->companyRestrictedId) {
            $faultedVar = "is".ucfirst("company")."Faulted";
            if (!$this->$faultedVar) {
                return NULL;
            }
            $this->$faultedVar = false; // TODO : set to false in the hydrator too
            $reqCtx = $reqCtx->copyWithoutRequestedFields();
            $qryContext = new \Core\Context\FindQueryContext("TestCompany", $reqCtx);
            $qryContext->addFields("id","users");
            $qryContext->addFilter(new \Core\Filter\StringFilter("TestCompany","","users.id = :id"), $this->getId());
            $qryContext->findAll();
            // this query will hydrate TestUser and TestCompany
            // RestrictedObjectHydrator will automatically hydrate companyRestrictedId
            // Since Doctrine shares model instances, companyRestrictedId will be automatically available
        }

        return $this->company && $this->company->getId() == $this->companyRestrictedId ? $this->company : NULL;
    }

    /**
     * Get company
     *
     * @return \Core\Model\TestCompany
     */
    public function getUnrestrictedCompany()
    {
        return $this->company;
    }

    /**
     * @var mixed
     */
    protected $lastLoginDate;

    /**
     * Get lastLoginDate
     *
     * @return mixed
     */
    public function getLastLoginDate()
    {
        return $this->lastLoginDate;
    }

    /**
     * Set lastLoginDate
     *
     * @param mixed $lastLoginDate
     *
     * @return TestUser
     */
    public function setLastLoginDate($lastLoginDate)
    {
        $this->lastLoginDate = $lastLoginDate;

        $class = get_class($this);
        $entity = ($pos = strrpos($class, "\\")) ? substr($class, $pos + 1) : $class;
        $appCtx = \Core\Context\ApplicationContext::getInstance();

        $this->lastLoginDate = $appCtx->getCalculatedField($entity, "lastLoginDate")->execute($this);

        return $this;
    }
}

