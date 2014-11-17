<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * SharedHostedProject
 */
class SharedHostedProject {

    /**
     * @var string
     */
    private $permission;

    /**
     * @var \Core\Model\User
     */
    private $participant;

    /**
     * @var \Core\Model\HostedProject
     */
    private $hostedProject;

    /**
     * Get permission
     *
     * @return string
     */
    public function getPermission () {

        return $this->permission;
    }

    /**
     * Set permission
     *
     * @param string $permission
     *
     * @return SharedHostedProject
     */
    public function setPermission ($permission) {

        $this->permission = $permission;

        return $this;
    }

    /**
     * Get participant
     *
     * @return \Core\Model\User
     */
    public function getParticipant () {

        return $this->participant;
    }

    /**
     * Set participant
     *
     * @param \Core\Model\User $participant
     *
     * @return SharedHostedProject
     */
    public function setParticipant (\Core\Model\User $participant) {

        $this->participant = $participant;

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
     * @return SharedHostedProject
     */
    public function setHostedProject (\Core\Model\HostedProject $hostedProject) {

        $this->hostedProject = $hostedProject;

        return $this;
    }
}
