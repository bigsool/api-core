<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * TestUser
 *
 * @ORM\Table(name="user", uniqueConstraints={@ORM\UniqueConstraint(name="userEmail", columns={"email"})})
 * @ORM\Entity
 */
class TestUser
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
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255, nullable=true)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lang", type="string", length=255, nullable=true)
     */
    private $lang;

    /**
     * @var string
     *
     * @ORM\Column(name="SALT", type="string", length=255, nullable=true)
     */
    private $salt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="register_date", type="datetime", nullable=false)
     */
    private $registerDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_login_date", type="datetime", nullable=true)
     */
    private $lastLoginDate;

    /**
     * @var string
     *
     * @ORM\Column(name="knowsFrom", type="string", length=255, nullable=true)
     */
    private $knowsFrom;

    /**
     * @var string
     *
     * @ORM\Column(name="confirmationKey", type="string", length=255, nullable=true)
     */
    private $confirmationKey;

    /**
     * @var \Core\Model\TestCompany
     *
     * @ORM\OneToOne(targetEntity="Core\Model\TestCompany", mappedBy="owner", cascade={"persist"})
     */
    private $ownedCompany;

    /**
     * @var \Core\Model\TestCompany
     *
     * @ORM\ManyToOne(targetEntity="Core\Model\TestCompany", inversedBy="users", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $company;


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
     * Set email
     *
     * @param string $email
     * @return TestUser
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
     * Set password
     *
     * @param string $password
     * @return TestUser
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
     * Set name
     *
     * @param string $name
     * @return TestUser
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
     * Set firstname
     *
     * @param string $firstname
     * @return TestUser
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lang
     *
     * @param string $lang
     * @return TestUser
     */
    public function setLang($lang)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * Get lang
     *
     * @return string 
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return TestUser
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string 
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set registerDate
     *
     * @param \DateTime $registerDate
     * @return TestUser
     */
    public function setRegisterDate($registerDate)
    {
        $this->registerDate = $registerDate;

        return $this;
    }

    /**
     * Get registerDate
     *
     * @return \DateTime 
     */
    public function getRegisterDate()
    {
        return $this->registerDate;
    }

    /**
     * Set lastLoginDate
     *
     * @param \DateTime $lastLoginDate
     * @return TestUser
     */
    public function setLastLoginDate($lastLoginDate)
    {
        $this->lastLoginDate = $lastLoginDate;

        return $this;
    }

    /**
     * Get lastLoginDate
     *
     * @return \DateTime 
     */
    public function getLastLoginDate()
    {
        return $this->lastLoginDate;
    }

    /**
     * Set knowsFrom
     *
     * @param string $knowsFrom
     * @return TestUser
     */
    public function setKnowsFrom($knowsFrom)
    {
        $this->knowsFrom = $knowsFrom;

        return $this;
    }

    /**
     * Get knowsFrom
     *
     * @return string 
     */
    public function getKnowsFrom()
    {
        return $this->knowsFrom;
    }

    /**
     * Set confirmationKey
     *
     * @param string $confirmationKey
     * @return TestUser
     */
    public function setConfirmationKey($confirmationKey)
    {
        $this->confirmationKey = $confirmationKey;

        return $this;
    }

    /**
     * Get confirmationKey
     *
     * @return string 
     */
    public function getConfirmationKey()
    {
        return $this->confirmationKey;
    }

    /**
     * Set ownedCompany
     *
     * @param \Core\Model\TestCompany $ownedCompany
     * @return TestUser
     */
    public function setOwnedCompany(\Core\Model\TestCompany $ownedCompany = null)
    {
        $this->ownedCompany = $ownedCompany;

        return $this;
    }

    /**
     * Get ownedCompany
     *
     * @return \Core\Model\TestCompany 
     */
    public function getOwnedCompany()
    {
        return $this->ownedCompany;
    }

    /**
     * Set company
     *
     * @param \Core\Model\TestCompany $company
     * @return TestUser
     */
    public function setCompany(\Core\Model\TestCompany $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return \Core\Model\TestCompany 
     */
    public function getCompany()
    {
        return $this->company;
    }
}
