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
}

