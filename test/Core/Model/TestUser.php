<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * TestUser
 *
 * @ORM\Table(name="user", uniqueConstraints={@ORM\UniqueConstraint(name="userEmail", columns={"email"})})
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
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    protected $password;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255, nullable=true)
     */
    protected $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lang", type="string", length=255, nullable=true)
     */
    protected $lang;

    /**
     * @var string
     *
     * @ORM\Column(name="SALT", type="string", length=255, nullable=true)
     */
    protected $salt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="register_date", type="datetime", nullable=false)
     */
    protected $registerDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_login_date", type="datetime", nullable=true)
     */
    protected $lastLoginDate;

    /**
     * @var string
     *
     * @ORM\Column(name="knowsFrom", type="string", length=255, nullable=true)
     */
    protected $knowsFrom;

    /**
     * @var string
     *
     * @ORM\Column(name="confirmationKey", type="string", length=255, nullable=true)
     */
    protected $confirmationKey;

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
     * @var \Core\Model\TestStorage
     *
     * @ORM\OneToOne(targetEntity="Core\Model\TestStorage", inversedBy="user", cascade={"persist","remove"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="storage_id", referencedColumnName="id", unique=true, nullable=true, onDelete="CASCADE")
     * })
     */
    protected $storage;

    /**
     * @var int
     */
    protected $storageRestrictedId;

    /**
     * @var bool
     */
    protected $isStorageFaulted = true;

    /**
     * @var \Core\Model\TestCompany
     *
     * @ORM\ManyToOne(targetEntity="Core\Model\TestCompany", inversedBy="users", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return TestUser
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return TestUser
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
     * Set name
     *
     * @param string $name
     *
     * @return TestUser
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return TestUser
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
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
     * Set salt
     *
     * @param string $salt
     *
     * @return TestUser
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set registerDate
     *
     * @param \DateTime $registerDate
     *
     * @return TestUser
     */
    public function setRegisterDate($registerDate)
    {
        $this->registerDate = $registerDate;

        return $this;
    }

    /**
     * Get registerDate
     *
     * @return \DateTime
     */
    public function getRegisterDate()
    {
        return $this->registerDate;
    }

    /**
     * Set lastLoginDate
     *
     * @param \DateTime $lastLoginDate
     *
     * @return TestUser
     */
    public function setLastLoginDate($lastLoginDate)
    {
        $this->lastLoginDate = $lastLoginDate;

        return $this;
    }

    /**
     * Get lastLoginDate
     *
     * @return \DateTime
     */
    public function getLastLoginDate()
    {
        return $this->lastLoginDate;
    }

    /**
     * Set knowsFrom
     *
     * @param string $knowsFrom
     *
     * @return TestUser
     */
    public function setKnowsFrom($knowsFrom)
    {
        $this->knowsFrom = $knowsFrom;

        return $this;
    }

    /**
     * Get knowsFrom
     *
     * @return string
     */
    public function getKnowsFrom()
    {
        return $this->knowsFrom;
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
     * Set storage
     *
     * @param \Core\Model\TestStorage $storage
     *
     * @return TestUser
     */
    public function setStorage(\Core\Model\TestStorage $storage = null)
    {
        $this->storage = $storage;
        $this->storageRestrictedId = $storage ? $storage->getId() : NULL;

        return $this;
    }

    /**
     * Get storage
     *
     * @return \Core\Model\TestStorage
     */
    public function getStorage()
    {

        $reqCtx = $this->findQueryContext ? $this->findQueryContext->getRequestContext() : \Core\Context\ApplicationContext::getInstance()->getInitialRequestContext();

        if (!$this->storageRestrictedId) {
            $faultedVar = "is".ucfirst("storage")."Faulted";
            if (!$this->$faultedVar) {
                return NULL;
            }
            $this->$faultedVar = false; // TODO : set to false in the hydrator too
            $reqCtx = $reqCtx->copyWithoutRequestedFields();
            $qryContext = new \Core\Context\FindQueryContext("TestStorage", $reqCtx);
            $qryContext->addFields("id","user");
            $qryContext->addFilter(new \Core\Filter\StringFilter("TestStorage","","user.id = :id"), $this->getId());
            $qryContext->findAll();
            // this query will hydrate TestUser and TestStorage
            // RestrictedObjectHydrator will automatically hydrate storageRestrictedId
            // Since Doctrine shares model instances, storageRestrictedId will be automatically available
        }

        return $this->storage && $this->storage->getId() == $this->storageRestrictedId ? $this->storage : NULL;
    }

    /**
     * Get storage
     *
     * @return \Core\Model\TestStorage
     */
    public function getUnrestrictedStorage()
    {
        return $this->storage;
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
}

