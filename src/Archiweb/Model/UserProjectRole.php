<?php

namespace Archiweb\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserProjectRole
 */
class UserProjectRole {

    /**
     * @var \Archiweb\Model\ProjectRole
     */
    private $projectRole;

    /**
     * @var \Archiweb\Model\User
     */
    private $user;

    /**
     * @var \Archiweb\Model\HostedProject
     */
    private $hostedProject;

    /**
     * Get projectRole
     *
     * @return \Archiweb\Model\ProjectRole
     */
    public function getProjectRole () {

        return $this->projectRole;
    }

    /**
     * Set projectRole
     *
     * @param \Archiweb\Model\ProjectRole $projectRole
     *
     * @return UserProjectRole
     */
    public function setProjectRole (\Archiweb\Model\ProjectRole $projectRole) {

        $this->projectRole = $projectRole;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Archiweb\Model\User
     */
    public function getUser () {

        return $this->user;
    }

    /**
     * Set user
     *
     * @param \Archiweb\Model\User $user
     *
     * @return UserProjectRole
     */
    public function setUser (\Archiweb\Model\User $user) {

        $this->user = $user;

        return $this;
    }

    /**
     * Get hostedProject
     *
     * @return \Archiweb\Model\HostedProject
     */
    public function getHostedProject () {

        return $this->hostedProject;
    }

    /**
     * Set hostedProject
     *
     * @param \Archiweb\Model\HostedProject $hostedProject
     *
     * @return UserProjectRole
     */
    public function setHostedProject (\Archiweb\Model\HostedProject $hostedProject) {

        $this->hostedProject = $hostedProject;

        return $this;
    }
}
