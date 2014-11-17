<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectRole
 */
class ProjectRole {

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
    public function __construct () {

        $this->userProjectRoles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->companiesForWhichThisProjectRoleIsDefault = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Get label
     *
     * @return string
     */
    public function getLabel () {

        return $this->label;
    }

    /**
     * Set label
     *
     * @param string $label
     *
     * @return ProjectRole
     */
    public function setLabel ($label) {

        $this->label = $label;

        return $this;
    }

    /**
     * Add userProjectRoles
     *
     * @param \Core\Model\UserProjectRole $userProjectRoles
     *
     * @return ProjectRole
     */
    public function addUserProjectRole (\Core\Model\UserProjectRole $userProjectRoles) {

        $this->userProjectRoles[] = $userProjectRoles;

        return $this;
    }

    /**
     * Remove userProjectRoles
     *
     * @param \Core\Model\UserProjectRole $userProjectRoles
     */
    public function removeUserProjectRole (\Core\Model\UserProjectRole $userProjectRoles) {

        $this->userProjectRoles->removeElement($userProjectRoles);
    }

    /**
     * Get userProjectRoles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserProjectRoles () {

        return $this->userProjectRoles;
    }

    /**
     * Add companiesForWhichThisProjectRoleIsDefault
     *
     * @param \Core\Model\Company $companiesForWhichThisProjectRoleIsDefault
     *
     * @return ProjectRole
     */
    public function addCompaniesForWhichThisProjectRoleIsDefault (\Core\Model\Company $companiesForWhichThisProjectRoleIsDefault) {

        $this->companiesForWhichThisProjectRoleIsDefault[] = $companiesForWhichThisProjectRoleIsDefault;

        return $this;
    }

    /**
     * Remove companiesForWhichThisProjectRoleIsDefault
     *
     * @param \Core\Model\Company $companiesForWhichThisProjectRoleIsDefault
     */
    public function removeCompaniesForWhichThisProjectRoleIsDefault (\Core\Model\Company $companiesForWhichThisProjectRoleIsDefault) {

        $this->companiesForWhichThisProjectRoleIsDefault->removeElement($companiesForWhichThisProjectRoleIsDefault);
    }

    /**
     * Get companiesForWhichThisProjectRoleIsDefault
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCompaniesForWhichThisProjectRoleIsDefault () {

        return $this->companiesForWhichThisProjectRoleIsDefault;
    }
}
