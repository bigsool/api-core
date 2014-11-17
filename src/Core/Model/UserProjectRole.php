<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserProjectRole
 */
class UserProjectRole {

    /**
     * @var \Core\Model\ProjectRole
     */
    private $projectRole;

    /**
     * @var \Core\Model\User
     */
    private $user;

    /**
     * @var \Core\Model\HostedProject
     */
    private $hostedProject;

    /**
     * Get projectRole
     *
     * @return \Core\Model\ProjectRole
     */
    public function getProjectRole () {

        return $this->projectRole;
    }

    /**
     * Set projectRole
     *
     * @param \Core\Model\ProjectRole $projectRole
     *
     * @return UserProjectRole
     */
    public function setProjectRole (\Core\Model\ProjectRole $projectRole) {

        $this->projectRole = $projectRole;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Core\Model\User
     */
    public function getUser () {

        return $this->user;
    }

    /**
     * Set user
     *
     * @param \Core\Model\User $user
     *
     * @return UserProjectRole
     */
    public function setUser (\Core\Model\User $user) {

        $this->user = $user;

        return $this;
    }

    /**
     * Get hostedProject
     *
     * @return \Core\Model\HostedProject
     */
    public function getHostedProject () {

        return $this->hostedProject;
    }

    /**
     * Set hostedProject
     *
     * @param \Core\Model\HostedProject $hostedProject
     *
     * @return UserProjectRole
     */
    public function setHostedProject (\Core\Model\HostedProject $hostedProject) {

        $this->hostedProject = $hostedProject;

        return $this;
    }
}
