<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * ABPerson
 *
 * @ORM\Table(name="abperson")
 * @ORM\Entity
 */
class ABPerson {

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
     * @ORM\Column(name="lastName", type="string", length=255, nullable=false)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="firstName", type="string", length=255, nullable=false)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Core\Model\ABCompanyContact", mappedBy="abcompany", cascade={"persist"})
     */
    private $abcompanyContact;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Core\Model\ABPersonContact", mappedBy="abperson", cascade={"persist"})
     */
    private $abpersonContact;

    /**
     * @var \Core\Model\ABCompany
     *
     * @ORM\ManyToOne(targetEntity="Core\Model\ABCompany", inversedBy="persons")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="abcompany_id", referencedColumnName="id")
     * })
     */
    private $abcompany;

    /**
     * Constructor
     */
    public function __construct () {

        $this->abcompanyContact = new \Doctrine\Common\Collections\ArrayCollection();
        $this->abpersonContact = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Get lastName
     *
     * @return string
     */
    public function getLastName () {

        return $this->lastName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return ABPerson
     */
    public function setLastName ($lastName) {

        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName () {

        return $this->firstName;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return ABPerson
     */
    public function setFirstName ($firstName) {

        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle () {

        return $this->title;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return ABPerson
     */
    public function setTitle ($title) {

        $this->title = $title;

        return $this;
    }

    /**
     * Add abcompanyContact
     *
     * @param \Core\Model\ABCompanyContact $abcompanyContact
     *
     * @return ABPerson
     */
    public function addAbcompanyContact (\Core\Model\ABCompanyContact $abcompanyContact) {

        $this->abcompanyContact[] = $abcompanyContact;

        return $this;
    }

    /**
     * Remove abcompanyContact
     *
     * @param \Core\Model\ABCompanyContact $abcompanyContact
     */
    public function removeAbcompanyContact (\Core\Model\ABCompanyContact $abcompanyContact) {

        $this->abcompanyContact->removeElement($abcompanyContact);
    }

    /**
     * Get abcompanyContact
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAbcompanyContact () {

        return $this->abcompanyContact;
    }

    /**
     * Add abpersonContact
     *
     * @param \Core\Model\ABPersonContact $abpersonContact
     *
     * @return ABPerson
     */
    public function addAbpersonContact (\Core\Model\ABPersonContact $abpersonContact) {

        $this->abpersonContact[] = $abpersonContact;

        return $this;
    }

    /**
     * Remove abpersonContact
     *
     * @param \Core\Model\ABPersonContact $abpersonContact
     */
    public function removeAbpersonContact (\Core\Model\ABPersonContact $abpersonContact) {

        $this->abpersonContact->removeElement($abpersonContact);
    }

    /**
     * Get abpersonContact
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAbpersonContact () {

        return $this->abpersonContact;
    }

    /**
     * Get abcompany
     *
     * @return \Core\Model\ABCompany
     */
    public function getAbcompany () {

        return $this->abcompany;
    }

    /**
     * Set abcompany
     *
     * @param \Core\Model\ABCompany $abcompany
     *
     * @return ABPerson
     */
    public function setAbcompany (\Core\Model\ABCompany $abcompany = NULL) {

        $this->abcompany = $abcompany;

        return $this;
    }
}
