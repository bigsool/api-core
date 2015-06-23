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
     * @ORM\Column(name="address", type="text", nullable=true)
     */
    protected $address;

    /**
     * @var string
     *
     * @ORM\Column(name="zipCode", type="string", length=255, nullable=true)
     */
    protected $zipCode;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     */
    protected $city;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=255, nullable=true)
     */
    protected $state;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255, nullable=true)
     */
    protected $country;

    /**
     * @var string
     *
     * @ORM\Column(name="tel", type="string", length=255, nullable=true)
     */
    protected $tel;

    /**
     * @var string
     *
     * @ORM\Column(name="fax", type="string", length=255, nullable=true)
     */
    protected $fax;

    /**
     * @var string
     *
     * @ORM\Column(name="tva", type="string", length=255, nullable=true)
     */
    protected $tva;

    /**
     * @var \Core\Model\TestUser
     *
     * @ORM\OneToOne(targetEntity="Core\Model\TestUser", inversedBy="ownedCompany", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="owner_id", referencedColumnName="id", unique=true, nullable=true, onDelete="CASCADE")
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
     * @var \Core\Model\TestStorage
     *
     * @ORM\OneToOne(targetEntity="Core\Model\TestStorage", inversedBy="company", cascade={"persist","remove"})
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
     * Set address
     *
     * @param string $address
     *
     * @return TestCompany
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set zipCode
     *
     * @param string $zipCode
     *
     * @return TestCompany
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Get zipCode
     *
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return TestCompany
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set state
     *
     * @param string $state
     *
     * @return TestCompany
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return TestCompany
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set tel
     *
     * @param string $tel
     *
     * @return TestCompany
     */
    public function setTel($tel)
    {
        $this->tel = $tel;

        return $this;
    }

    /**
     * Get tel
     *
     * @return string
     */
    public function getTel()
    {
        return $this->tel;
    }

    /**
     * Set fax
     *
     * @param string $fax
     *
     * @return TestCompany
     */
    public function setFax($fax)
    {
        $this->fax = $fax;

        return $this;
    }

    /**
     * Get fax
     *
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set tva
     *
     * @param string $tva
     *
     * @return TestCompany
     */
    public function setTva($tva)
    {
        $this->tva = $tva;

        return $this;
    }

    /**
     * Get tva
     *
     * @return string
     */
    public function getTva()
    {
        return $this->tva;
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
        if (!$this->ownerRestrictedId && $this->findQueryContext) {
            $faultedVar = "is".ucfirst("owner")."Faulted";
            if (!$this->$faultedVar) {
                return NULL;
            }
            $this->$faultedVar = false; // TODO : set to false in the hydrator too
            $reqCtx = $this->findQueryContext->getRequestContext()->copyWithoutRequestedFields();
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
     * Set storage
     *
     * @param \Core\Model\TestStorage $storage
     *
     * @return TestCompany
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
        if (!$this->storageRestrictedId && $this->findQueryContext) {
            $faultedVar = "is".ucfirst("storage")."Faulted";
            if (!$this->$faultedVar) {
                return NULL;
            }
            $this->$faultedVar = false; // TODO : set to false in the hydrator too
            $reqCtx = $this->findQueryContext->getRequestContext()->copyWithoutRequestedFields();
            $qryContext = new \Core\Context\FindQueryContext("TestStorage", $reqCtx);
            $qryContext->addFields("id","company");
            $qryContext->addFilter(new \Core\Filter\StringFilter("TestStorage","","company.id = :id"), $this->getId());
            $qryContext->findAll();
            // this query will hydrate TestCompany and TestStorage
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

