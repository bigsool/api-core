<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * ABPersonContact
 *
 * @ORM\Table(name="abperson_contact", uniqueConstraints={@ORM\UniqueConstraint(name="abperson_contact_order", columns={"abperson_id", "contact_id", "order"})})
 * @ORM\Entity
 */
class ABPersonContact
{
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
     * @ORM\OneToOne(targetEntity="Core\Model\Contact", inversedBy="abpersonContact", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="contact_id", referencedColumnName="id", unique=true)
     * })
     */
    private $contact;

    /**
     * @var \Core\Model\ABPerson
     *
     * @ORM\ManyToOne(targetEntity="Core\Model\ABPerson", inversedBy="abpersonContact")
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
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return ABPersonContact
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
     * Set order
     *
     * @param integer $order
     * @return ABPersonContact
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return integer 
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set contact
     *
     * @param \Core\Model\Contact $contact
     * @return ABPersonContact
     */
    public function setContact(\Core\Model\Contact $contact = null)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact
     *
     * @return \Core\Model\Contact 
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set abperson
     *
     * @param \Core\Model\ABPerson $abperson
     * @return ABPersonContact
     */
    public function setAbperson(\Core\Model\ABPerson $abperson = null)
    {
        $this->abperson = $abperson;

        return $this;
    }

    /**
     * Get abperson
     *
     * @return \Core\Model\ABPerson 
     */
    public function getAbperson()
    {
        return $this->abperson;
    }
}
