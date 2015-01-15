<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Company
 *
 * @ORM\Table(name="company")
 * @ORM\Entity
 */
class Company {

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
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="text", nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="zipCode", type="string", length=255, nullable=true)
     */
    private $zipCode;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=255, nullable=true)
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255, nullable=true)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="tel", type="string", length=255, nullable=true)
     */
    private $tel;

    /**
     * @var string
     *
     * @ORM\Column(name="fax", type="string", length=255, nullable=true)
     */
    private $fax;

    /**
     * @var string
     *
     * @ORM\Column(name="tva", type="string", length=255, nullable=true)
     */
    private $tva;

    /**
     * @var \Core\Model\User
     *
     * @ORM\OneToOne(targetEntity="Core\Model\User", inversedBy="ownedCompany", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="owner_id", referencedColumnName="id", unique=true, nullable=true)
     * })
     */
    private $owner;

    /**
     * @var \Core\Model\Storage
     *
     * @ORM\OneToOne(targetEntity="Core\Model\Storage", mappedBy="company", cascade={"persist"})
     */
    private $storage;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Core\Model\User", mappedBy="company", cascade={"persist"})
     */
    private $users;

    /**
     * Constructor
     */
    public function __construct () {

        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Get name
     *
     * @return string
     */
    public function getName () {

        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Company
     */
    public function setName ($name) {

        $this->name = $name;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress () {

        return $this->address;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Company
     */
    public function setAddress ($address) {

        $this->address = $address;

        return $this;
    }

    /**
     * Get zipCode
     *
     * @return string
     */
    public function getZipCode () {

        return $this->zipCode;
    }

    /**
     * Set zipCode
     *
     * @param string $zipCode
     *
     * @return Company
     */
    public function setZipCode ($zipCode) {

        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity () {

        return $this->city;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Company
     */
    public function setCity ($city) {

        $this->city = $city;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState () {

        return $this->state;
    }

    /**
     * Set state
     *
     * @param string $state
     *
     * @return Company
     */
    public function setState ($state) {

        $this->state = $state;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry () {

        return $this->country;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return Company
     */
    public function setCountry ($country) {

        $this->country = $country;

        return $this;
    }

    /**
     * Get tel
     *
     * @return string
     */
    public function getTel () {

        return $this->tel;
    }

    /**
     * Set tel
     *
     * @param string $tel
     *
     * @return Company
     */
    public function setTel ($tel) {

        $this->tel = $tel;

        return $this;
    }

    /**
     * Get fax
     *
     * @return string
     */
    public function getFax () {

        return $this->fax;
    }

    /**
     * Set fax
     *
     * @param string $fax
     *
     * @return Company
     */
    public function setFax ($fax) {

        $this->fax = $fax;

        return $this;
    }

    /**
     * Get tva
     *
     * @return string
     */
    public function getTva () {

        return $this->tva;
    }

    /**
     * Set tva
     *
     * @param string $tva
     *
     * @return Company
     */
    public function setTva ($tva) {

        $this->tva = $tva;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \Core\Model\User
     */
    public function getOwner () {

        return $this->owner;
    }

    /**
     * Set owner
     *
     * @param \Core\Model\User $owner
     *
     * @return Company
     */
    public function setOwner (\Core\Model\User $owner = NULL) {

        $this->owner = $owner;

        return $this;
    }

    /**
     * Get storage
     *
     * @return \Core\Model\Storage
     */
    public function getStorage () {

        return $this->storage;
    }

    /**
     * Set storage
     *
     * @param \Core\Model\Storage $storage
     *
     * @return Company
     */
    public function setStorage (\Core\Model\Storage $storage = NULL) {

        $this->storage = $storage;

        return $this;
    }

    /**
     * Add users
     *
     * @param \Core\Model\User $users
     *
     * @return Company
     */
    public function addUser (\Core\Model\User $users) {

        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \Core\Model\User $users
     */
    public function removeUser (\Core\Model\User $users) {

        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers () {

        return $this->users;
    }
}
