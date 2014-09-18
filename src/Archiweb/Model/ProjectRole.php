<?php

namespace Archiweb\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectRole
 */
class ProjectRole
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $label;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $userProjectRoles;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $companiesForWhichThisProjectRoleIsDefault;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->userProjectRoles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->companiesForWhichThisProjectRoleIsDefault = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @param \Archiweb\Model\UserProjectRole $userProjectRoles
     * @return ProjectRole
     */
    public function addUserProjectRole(\Archiweb\Model\UserProjectRole $userProjectRoles)
    {
        $this->userProjectRoles[] = $userProjectRoles;

        return $this;
    }

    /**
     * Remove userProjectRoles
     *
     * @param \Archiweb\Model\UserProjectRole $userProjectRoles
     */
    public function removeUserProjectRole(\Archiweb\Model\UserProjectRole $userProjectRoles)
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
     * Add companiesForWhichThisProjectRoleIsDefault
     *
     * @param \Archiweb\Model\Company $companiesForWhichThisProjectRoleIsDefault
     * @return ProjectRole
     */
    public function addCompaniesForWhichThisProjectRoleIsDefault(\Archiweb\Model\Company $companiesForWhichThisProjectRoleIsDefault)
    {
        $this->companiesForWhichThisProjectRoleIsDefault[] = $companiesForWhichThisProjectRoleIsDefault;

        return $this;
    }

    /**
     * Remove companiesForWhichThisProjectRoleIsDefault
     *
     * @param \Archiweb\Model\Company $companiesForWhichThisProjectRoleIsDefault
     */
    public function removeCompaniesForWhichThisProjectRoleIsDefault(\Archiweb\Model\Company $companiesForWhichThisProjectRoleIsDefault)
    {
        $this->companiesForWhichThisProjectRoleIsDefault->removeElement($companiesForWhichThisProjectRoleIsDefault);
    }

    /**
     * Get companiesForWhichThisProjectRoleIsDefault
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCompaniesForWhichThisProjectRoleIsDefault()
    {
        return $this->companiesForWhichThisProjectRoleIsDefault;
    }
}
