<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Contact
 *
 * @ORM\Table(name="contact")
 * @ORM\Entity
 */
class Contact
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
     * @ORM\Column(name="label", type="string", length=255)
     */
    private $label;

    /**
     * @var string
     *
     * @ORM\Column(name="streets", type="text", length=65535)
     */
    private $streets;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=255)
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="zip", type="string", length=255)
     */
    private $zip;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="string", length=255)
     */
    private $mobile;

    /**
     * @var string
     *
     * @ORM\Column(name="landLine", type="string", length=255)
     */
    private $landLine;

    /**
     * @var \Core\Model\ABCompanyContact
     *
     * @ORM\OneToOne(targetEntity="Core\Model\ABCompanyContact", mappedBy="contact", cascade={"persist"})
     */
    private $abcompanyContact;

    /**
     * @var \Core\Model\ABPersonContact
     *
     * @ORM\OneToOne(targetEntity="Core\Model\ABPersonContact", mappedBy="contact", cascade={"persist"})
     */
    private $abpersonContact;


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
     * Set label
     *
     * @param string $label
     * @return Contact
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set streets
     *
     * @param string $streets
     * @return Contact
     */
    public function setStreets($streets)
    {
        $this->streets = $streets;

        return $this;
    }

    /**
     * Get streets
     *
     * @return string 
     */
    public function getStreets()
    {
        return $this->streets;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Contact
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
     * @return Contact
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
     * Set zip
     *
     * @param string $zip
     * @return Contact
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip
     *
     * @return string 
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return Contact
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
     * Set email
     *
     * @param string $email
     * @return Contact
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set mobile
     *
     * @param string $mobile
     * @return Contact
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * Get mobile
     *
     * @return string 
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * Set landLine
     *
     * @param string $landLine
     * @return Contact
     */
    public function setLandLine($landLine)
    {
        $this->landLine = $landLine;

        return $this;
    }

    /**
     * Get landLine
     *
     * @return string 
     */
    public function getLandLine()
    {
        return $this->landLine;
    }

    /**
     * Set abcompanyContact
     *
     * @param \Core\Model\ABCompanyContact $abcompanyContact
     * @return Contact
     */
    public function setAbcompanyContact(\Core\Model\ABCompanyContact $abcompanyContact = null)
    {
        $this->abcompanyContact = $abcompanyContact;

        return $this;
    }

    /**
     * Get abcompanyContact
     *
     * @return \Core\Model\ABCompanyContact 
     */
    public function getAbcompanyContact()
    {
        return $this->abcompanyContact;
    }

    /**
     * Set abpersonContact
     *
     * @param \Core\Model\ABPersonContact $abpersonContact
     * @return Contact
     */
    public function setAbpersonContact(\Core\Model\ABPersonContact $abpersonContact = null)
    {
        $this->abpersonContact = $abpersonContact;

        return $this;
    }

    /**
     * Get abpersonContact
     *
     * @return \Core\Model\ABPersonContact 
     */
    public function getAbpersonContact()
    {
        return $this->abpersonContact;
    }
}
