<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * TestCredential
 *
 * @ORM\Table(name="credential")
 * @ORM\Entity
 */
class TestCredential
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
     * @ORM\Column(name="type", type="string", length=255, nullable=false)
     */
    protected $type;

    /**
     * @var string
     *
     * @ORM\Column(name="login", type="string", length=255, nullable=false, unique=true)
     */
    protected $login;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    protected $password;

    /**
     * @var \Core\Model\TestUser
     *
     * @ORM\OneToOne(targetEntity="Core\Model\TestUser", mappedBy="credential", cascade={"persist"})
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
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Core\Model\TestLoginHistory", mappedBy="credential", cascade={"persist"})
     */
    protected $loginHistories;

    /**
     * @var int[]
     */
    protected $loginHistoriesRestrictedIds = [];

    /**
     * @var bool
     */
    protected $isLoginHistoriesFaulted = true;

    /**
     * Constructor
     * @internal You don't have to explicitly call the constructor of this entity. Use the ModuleEntity instead.
     */
    public function __construct()
    {
        $this->loginHistories = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set type
     *
     * @param string $type
     *
     * @return TestCredential
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set login
     *
     * @param string $login
     *
     * @return TestCredential
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
     *
     * @return TestCredential
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
     * Set user
     *
     * @param \Core\Model\TestUser $user
     *
     * @return TestCredential
     */
    public function setUser(\Core\Model\TestUser $user = null)
    {
        $this->user = $user;
        $this->userRestrictedId = $user ? $user->getId() : NULL;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \Core\Model\TestUser
     */
    public function getUser()
    {
    
        $reqCtx = $this->findQueryContext ? $this->findQueryContext->getRequestContext() : \Core\Context\ApplicationContext::getInstance()->getInitialRequestContext();
    
        if (!$this->userRestrictedId) {
            $faultedVar = "is".ucfirst("user")."Faulted";
            if (!$this->$faultedVar) {
                return NULL;
            }
            $this->$faultedVar = false; // TODO : set to false in the hydrator too
            $reqCtx = $reqCtx->copyWithoutRequestedFields();
            $qryContext = new \Core\Context\FindQueryContext("TestUser", $reqCtx);
            $qryContext->addFields("id","credential");
            $qryContext->addFilter(new \Core\Filter\StringFilter("TestUser","","credential.id = :id"), $this->getId());
            $qryContext->findAll();
            // this query will hydrate TestCredential and TestUser
            // RestrictedObjectHydrator will automatically hydrate userRestrictedId
            // Since Doctrine shares model instances, userRestrictedId will be automatically available
        }
    
        return $this->user && $this->user->getId() == $this->userRestrictedId ? $this->user : NULL;
    }

    /**
     * Get user
     *
     * @return \Core\Model\TestUser
     */
    public function getUnrestrictedUser()
    {
        return $this->user;
    }

    /**
     * Add loginHistory
     *
     * @param \Core\Model\TestLoginHistory $loginHistory
     *
     * @return TestCredential
     */
    public function addLoginHistory(\Core\Model\TestLoginHistory $loginHistory)
    {
        $this->loginHistories[] = $loginHistory;
        $this->loginHistoriesRestrictedIds[] = $loginHistory->getId();
    
        return $this;
    }

    /**
     * Remove loginHistory
     *
     * @param \Core\Model\TestLoginHistory $loginHistory
     */
    public function removeLoginHistory(\Core\Model\TestLoginHistory $loginHistory)
    {
        $this->loginHistories->removeElement($loginHistory);
        $this->loginHistoriesRestrictedIds = array_diff($this->loginHistoriesRestrictedIds,[$loginHistory->getId()]);
    }

    /**
     * Get loginHistories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLoginHistories()
    {
    
        $reqCtx = $this->findQueryContext ? $this->findQueryContext->getRequestContext() : \Core\Context\ApplicationContext::getInstance()->getInitialRequestContext();
    
        if (!$this->loginHistoriesRestrictedIds) {
            $faultedVar = "is".ucfirst("loginHistories")."Faulted";
            if ($this->$faultedVar) {
                $this->$faultedVar = false; // TODO : set to false in the hydrator too
                $reqCtx = $reqCtx->copyWithoutRequestedFields();
                $qryContext = new \Core\Context\FindQueryContext("TestLoginHistory", $reqCtx);
                $qryContext->addFields("id","credential");
                $qryContext->addFilter(new \Core\Filter\StringFilter("TestLoginHistory","","credential.id = :id"), $this->getId());
                $qryContext->findAll();
                // this query will hydrate TestCredential and TestLoginHistory
                // RestrictedObjectHydrator will automatically hydrate loginHistoriesRestrictedId
                // Since Doctrine shares model instances, loginHistoriesRestrictedId will be automatically available
            }
        }
    
        $inExpr = \Doctrine\Common\Collections\Criteria::expr()->in("id", $this->loginHistoriesRestrictedIds);
    
        $criteria = \Doctrine\Common\Collections\Criteria::create();
        $criteria->where($inExpr);
    
        return $this->loginHistories->matching($criteria);
    }

    /**
     * Get loginHistories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUnrestrictedLoginHistories()
    {
        return $this->loginHistories;
    }
}

