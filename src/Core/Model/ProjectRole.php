<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectRole
 *
 * @ORM\Table(name="projectRole")
 * @ORM\Entity
 */
class ProjectRole
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
     * @ORM\Column(name="label", type="string", length=255, nullable=false)
     */
    private $label;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Core\Model\UserProjectRole", mappedBy="projectRole")
     */
    private $userProjectRoles;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->userProjectRoles = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set label
     *
     * @param string $label
     * @return ProjectRole
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
     * Add userProjectRoles
     *
     * @param \Core\Model\UserProjectRole $userProjectRoles
     * @return ProjectRole
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
}
