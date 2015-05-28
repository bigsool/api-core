<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * ABCompany
 *
 * @ORM\Table(name="abcompany")
 * @ORM\Entity
 */
class ABCompany {

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
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Core\Model\ABPerson", mappedBy="abcompany", cascade={"persist"})
     */
    private $persons;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Core\Model\ABCompanyContact", mappedBy="abcompany", cascade={"persist"})
     */
    private $abcompanyContact;

    /**
     * Constructor
     */
    public function __construct () {

        $this->persons = new \Doctrine\Common\Collections\ArrayCollection();
        $this->abcompanyContact = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return ABCompany
     */
    public function setName ($name) {

        $this->name = $name;

        return $this;
    }

    /**
     * Add persons
     *
     * @param \Core\Model\ABPerson $persons
     *
     * @return ABCompany
     */
    public function addPerson (\Core\Model\ABPerson $persons) {

        $this->persons[] = $persons;

        return $this;
    }

    /**
     * Remove persons
     *
     * @param \Core\Model\ABPerson $persons
     */
    public function removePerson (\Core\Model\ABPerson $persons) {

        $this->persons->removeElement($persons);
    }

    /**
     * Get persons
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPersons () {

        return $this->persons;
    }

    /**
     * Add abcompanyContact
     *
     * @param \Core\Model\ABCompanyContact $abcompanyContact
     *
     * @return ABCompany
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
}
