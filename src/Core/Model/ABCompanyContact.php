<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * ABCompanyContact
 *
 * @ORM\Table(name="abcompany_contact", uniqueConstraints={@ORM\UniqueConstraint(name="abcompany_contact_order", columns={"abcompany_id", "contact_id", "order"})})
 * @ORM\Entity
 */
class ABCompanyContact {

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
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="order", type="integer")
     */
    private $order;

    /**
     * @var \Core\Model\Contact
     *
     * @ORM\OneToOne(targetEntity="Core\Model\Contact", inversedBy="abcompanyContact", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="contact_id", referencedColumnName="id", unique=true)
     * })
     */
    private $contact;

    /**
     * @var \Core\Model\ABCompany
     *
     * @ORM\ManyToOne(targetEntity="Core\Model\ABCompany", inversedBy="abcompanyContact")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="abcompany_id", referencedColumnName="id")
     * })
     */
    private $abcompany;

    /**
     * @var \Core\Model\ABPerson
     *
     * @ORM\ManyToOne(targetEntity="Core\Model\ABPerson", inversedBy="abcompanyContact")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="abperson_id", referencedColumnName="id")
     * })
     */
    private $abperson;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId () {

        return $this->id;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType () {

        return $this->type;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return ABCompanyContact
     */
    public function setType ($type) {

        $this->type = $type;

        return $this;
    }

    /**
     * Get order
     *
     * @return integer
     */
    public function getOrder () {

        return $this->order;
    }

    /**
     * Set order
     *
     * @param integer $order
     *
     * @return ABCompanyContact
     */
    public function setOrder ($order) {

        $this->order = $order;

        return $this;
    }

    /**
     * Get contact
     *
     * @return \Core\Model\Contact
     */
    public function getContact () {

        return $this->contact;
    }

    /**
     * Set contact
     *
     * @param \Core\Model\Contact $contact
     *
     * @return ABCompanyContact
     */
    public function setContact (\Core\Model\Contact $contact = NULL) {

        $this->contact = $contact;

        return $this;
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
     * @return ABCompanyContact
     */
    public function setAbcompany (\Core\Model\ABCompany $abcompany = NULL) {

        $this->abcompany = $abcompany;

        return $this;
    }

    /**
     * Get abperson
     *
     * @return \Core\Model\ABPerson
     */
    public function getAbperson () {

        return $this->abperson;
    }

    /**
     * Set abperson
     *
     * @param \Core\Model\ABPerson $abperson
     *
     * @return ABCompanyContact
     */
    public function setAbperson (\Core\Model\ABPerson $abperson = NULL) {

        $this->abperson = $abperson;

        return $this;
    }
}
