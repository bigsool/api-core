<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * TestLoginHistory
 *
 * @ORM\Table(name="login_history")
 * @ORM\Entity
 */
class TestLoginHistory
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", length=255, nullable=false)
     */
    protected $date;

    /**
     * @var string
     *
     * @ORM\Column(name="IP", type="string", length=255, nullable=true)
     */
    protected $IP;

    /**
     * @var \Core\Model\TestCredential
     *
     * @ORM\ManyToOne(targetEntity="Core\Model\TestCredential", inversedBy="loginHistories", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="credential_id", referencedColumnName="id", nullable=false)
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return TestLoginHistory
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set iP
     *
     * @param string $iP
     *
     * @return TestLoginHistory
     */
    public function setIP($iP)
    {
        $this->IP = $iP;

        return $this;
    }

    /**
     * Get iP
     *
     * @return string
     */
    public function getIP()
    {
        return $this->IP;
    }

    /**
     * Set credential
     *
     * @param \Core\Model\TestCredential $credential
     *
     * @return TestLoginHistory
     */
    public function setCredential(\Core\Model\TestCredential $credential)
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
            $qryContext->addFields("id","loginHistories");
            $qryContext->addFilter(new \Core\Filter\StringFilter("TestCredential","","loginHistories.id = :id"), $this->getId());
            $qryContext->findAll();
            // this query will hydrate TestLoginHistory and TestCredential
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

