<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * HostedProject
 *
 * @ORM\Table(name="hostedProject")
 * @ORM\Entity
 */
class HostedProject
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint")
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
     * @var \DateTime
     *
     * @ORM\Column(name="creationDate", type="datetime", nullable=false)
     */
    private $creationDate;

    /**
     * @var string
     *
     * @ORM\Column(name="patchId", type="string", length=255, nullable=true)
     */
    private $patchId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastModificationDate", type="datetime", nullable=false)
     */
    private $lastModificationDate;

    /**
     * @var string
     *
     * @ORM\Column(name="clientNameCreator", type="string", length=255, nullable=false)
     */
    private $clientNameCreator;

    /**
     * @var string
     *
     * @ORM\Column(name="clientVersionCreator", type="string", length=255, nullable=false)
     */
    private $clientVersionCreator;

    /**
     * @var string
     *
     * @ORM\Column(name="UUIDCreator", type="string", length=255, nullable=true)
     */
    private $UUIDCreator;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isUploading", type="boolean", nullable=true)
     */
    private $isUploading;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isSynchronizable", type="boolean", nullable=false)
     */
    private $isSynchronizable;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Core\Model\UserProjectRole", mappedBy="hostedProject")
     */
    private $userProjectRoles;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Core\Model\SharedHostedProject", mappedBy="hostedProject", cascade={"persist"})
     */
    private $sharedHostedProjects;

    /**
     * @var \Core\Model\User
     *
     * @ORM\ManyToOne(targetEntity="Core\Model\User", inversedBy="hostedProjects", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="creator", referencedColumnName="id", nullable=false)
     * })
     */
    private $creator;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->userProjectRoles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->sharedHostedProjects = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return HostedProject
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
     * Set creationDate
     *
     * @param \DateTime $creationDate
     * @return HostedProject
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime 
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set patchId
     *
     * @param string $patchId
     * @return HostedProject
     */
    public function setPatchId($patchId)
    {
        $this->patchId = $patchId;

        return $this;
    }

    /**
     * Get patchId
     *
     * @return string 
     */
    public function getPatchId()
    {
        return $this->patchId;
    }

    /**
     * Set lastModificationDate
     *
     * @param \DateTime $lastModificationDate
     * @return HostedProject
     */
    public function setLastModificationDate($lastModificationDate)
    {
        $this->lastModificationDate = $lastModificationDate;

        return $this;
    }

    /**
     * Get lastModificationDate
     *
     * @return \DateTime 
     */
    public function getLastModificationDate()
    {
        return $this->lastModificationDate;
    }

    /**
     * Set clientNameCreator
     *
     * @param string $clientNameCreator
     * @return HostedProject
     */
    public function setClientNameCreator($clientNameCreator)
    {
        $this->clientNameCreator = $clientNameCreator;

        return $this;
    }

    /**
     * Get clientNameCreator
     *
     * @return string 
     */
    public function getClientNameCreator()
    {
        return $this->clientNameCreator;
    }

    /**
     * Set clientVersionCreator
     *
     * @param string $clientVersionCreator
     * @return HostedProject
     */
    public function setClientVersionCreator($clientVersionCreator)
    {
        $this->clientVersionCreator = $clientVersionCreator;

        return $this;
    }

    /**
     * Get clientVersionCreator
     *
     * @return string 
     */
    public function getClientVersionCreator()
    {
        return $this->clientVersionCreator;
    }

    /**
     * Set UUIDCreator
     *
     * @param string $uUIDCreator
     * @return HostedProject
     */
    public function setUUIDCreator($uUIDCreator)
    {
        $this->UUIDCreator = $uUIDCreator;

        return $this;
    }

    /**
     * Get UUIDCreator
     *
     * @return string 
     */
    public function getUUIDCreator()
    {
        return $this->UUIDCreator;
    }

    /**
     * Set isUploading
     *
     * @param boolean $isUploading
     * @return HostedProject
     */
    public function setIsUploading($isUploading)
    {
        $this->isUploading = $isUploading;

        return $this;
    }

    /**
     * Get isUploading
     *
     * @return boolean 
     */
    public function getIsUploading()
    {
        return $this->isUploading;
    }

    /**
     * Set isSynchronizable
     *
     * @param boolean $isSynchronizable
     * @return HostedProject
     */
    public function setIsSynchronizable($isSynchronizable)
    {
        $this->isSynchronizable = $isSynchronizable;

        return $this;
    }

    /**
     * Get isSynchronizable
     *
     * @return boolean 
     */
    public function getIsSynchronizable()
    {
        return $this->isSynchronizable;
    }

    /**
     * Add userProjectRoles
     *
     * @param \Core\Model\UserProjectRole $userProjectRoles
     * @return HostedProject
     */
    public function addUserProjectRole(\Core\Model\UserProjectRole $userProjectRoles)
    {
        $this->userProjectRoles[] = $userProjectRoles;

        return $this;
    }

    /**
     * Remove userProjectRoles
     *
     * @param \Core\Model\UserProjectRole $userProjectRoles
     */
    public function removeUserProjectRole(\Core\Model\UserProjectRole $userProjectRoles)
    {
        $this->userProjectRoles->removeElement($userProjectRoles);
    }

    /**
     * Get userProjectRoles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserProjectRoles()
    {
        return $this->userProjectRoles;
    }

    /**
     * Add sharedHostedProjects
     *
     * @param \Core\Model\SharedHostedProject $sharedHostedProjects
     * @return HostedProject
     */
    public function addSharedHostedProject(\Core\Model\SharedHostedProject $sharedHostedProjects)
    {
        $this->sharedHostedProjects[] = $sharedHostedProjects;

        return $this;
    }

    /**
     * Remove sharedHostedProjects
     *
     * @param \Core\Model\SharedHostedProject $sharedHostedProjects
     */
    public function removeSharedHostedProject(\Core\Model\SharedHostedProject $sharedHostedProjects)
    {
        $this->sharedHostedProjects->removeElement($sharedHostedProjects);
    }

    /**
     * Get sharedHostedProjects
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSharedHostedProjects()
    {
        return $this->sharedHostedProjects;
    }

    /**
     * Set creator
     *
     * @param \Core\Model\User $creator
     * @return HostedProject
     */
    public function setCreator(\Core\Model\User $creator)
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * Get creator
     *
     * @return \Core\Model\User 
     */
    public function getCreator()
    {
        return $this->creator;
    }
}
