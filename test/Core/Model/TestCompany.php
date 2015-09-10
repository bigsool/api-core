<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * TestCompany
 *
 * @ORM\Table(name="company")
 * @ORM\Entity
 */
class TestCompany
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
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="vat", type="string", length=255, nullable=true)
     */
    protected $vat;

    /**
     * @var \Core\Model\TestUser
     *
     * @ORM\OneToOne(targetEntity="Core\Model\TestUser", inversedBy="ownedCompany", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="owner_id", referencedColumnName="id", unique=true, nullable=true)
     * })
     */
    protected $owner;

    /**
     * @var int
     */
    protected $ownerRestrictedId;

    /**
     * @var bool
     */
    protected $isOwnerFaulted = true;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Core\Model\TestUser", mappedBy="company", cascade={"persist"})
     */
    protected $users;

    /**
     * @var int[]
     */
    protected $usersRestrictedIds = [];

    /**
     * @var bool
     */
    protected $isUsersFaulted = true;

    /**
     * Constructor
     * @internal You don't have to explicitly call the constructor of this entity. Use the ModuleEntity instead.
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return TestCompany
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
     * Set vat
     *
     * @param string $vat
     *
     * @return TestCompany
     */
    public function setVat($vat)
    {
        $this->vat = $vat;

        return $this;
    }

    /**
     * Get vat
     *
     * @return string
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * Set owner
     *
     * @param \Core\Model\TestUser $owner
     *
     * @return TestCompany
     */
    public function setOwner(\Core\Model\TestUser $owner = null)
    {
        $this->owner = $owner;
        $this->ownerRestrictedId = $owner ? $owner->getId() : NULL;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \Core\Model\TestUser
     */
    public function getOwner()
    {

        $reqCtx = $this->findQueryContext ? $this->findQueryContext->getRequestContext() : \Core\Context\ApplicationContext::getInstance()->getInitialRequestContext();

        if (!$this->ownerRestrictedId) {
            $faultedVar = "is".ucfirst("owner")."Faulted";
            if (!$this->$faultedVar) {
                return NULL;
            }
            $this->$faultedVar = false; // TODO : set to false in the hydrator too
            $reqCtx = $reqCtx->copyWithoutRequestedFields();
            $qryContext = new \Core\Context\FindQueryContext("TestUser", $reqCtx);
            $qryContext->addFields("id","ownedCompany");
            $qryContext->addFilter(new \Core\Filter\StringFilter("TestUser","","ownedCompany.id = :id"), $this->getId());
            $qryContext->findAll();
            // this query will hydrate TestCompany and TestUser
            // RestrictedObjectHydrator will automatically hydrate ownerRestrictedId
            // Since Doctrine shares model instances, ownerRestrictedId will be automatically available
        }

        return $this->owner && $this->owner->getId() == $this->ownerRestrictedId ? $this->owner : NULL;
    }

    /**
     * Get owner
     *
     * @return \Core\Model\TestUser
     */
    public function getUnrestrictedOwner()
    {
        return $this->owner;
    }

    /**
     * Add user
     *
     * @param \Core\Model\TestUser $user
     *
     * @return TestCompany
     */
    public function addUser(\Core\Model\TestUser $user)
    {
        $this->users[] = $user;
        $this->usersRestrictedIds[] = $user->getId();

        return $this;
    }

    /**
     * Remove user
     *
     * @param \Core\Model\TestUser $user
     */
    public function removeUser(\Core\Model\TestUser $user)
    {
        $this->users->removeElement($user);
        $this->usersRestrictedIds = array_diff($this->usersRestrictedIds,[$user->getId()]);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {

        $reqCtx = $this->findQueryContext ? $this->findQueryContext->getRequestContext() : \Core\Context\ApplicationContext::getInstance()->getInitialRequestContext();

        if (!$this->usersRestrictedIds) {
            $faultedVar = "is".ucfirst("users")."Faulted";
            if ($this->$faultedVar) {
                $this->$faultedVar = false; // TODO : set to false in the hydrator too
                $reqCtx = $reqCtx->copyWithoutRequestedFields();
                $qryContext = new \Core\Context\FindQueryContext("TestUser", $reqCtx);
                $qryContext->addFields("id","company");
                $qryContext->addFilter(new \Core\Filter\StringFilter("TestUser","","company.id = :id"), $this->getId());
                $qryContext->findAll();
                // this query will hydrate TestCompany and TestUser
                // RestrictedObjectHydrator will automatically hydrate usersRestrictedId
                // Since Doctrine shares model instances, usersRestrictedId will be automatically available
            }
        }

        // workaround to fix doctrine bug. I did a PR ( https://github.com/doctrine/doctrine2/pull/1501 )
        $users = [];
        foreach ($this->users as $entity) {
            if (in_array($entity->getId(), $this->usersRestrictedIds)) {
                $users[] = $entity;
            }
        }

        return new \Doctrine\Common\Collections\ArrayCollection($users);

        $inExpr = \Doctrine\Common\Collections\Criteria::expr()->in("id", $this->usersRestrictedIds);

        $criteria = \Doctrine\Common\Collections\Criteria::create();
        $criteria->where($inExpr);

        return $this->users->matching($criteria);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUnrestrictedUsers()
    {
        return $this->users;
    }
}

