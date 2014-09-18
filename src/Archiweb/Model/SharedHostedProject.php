<?php

namespace Archiweb\Model;

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
     * @var \Archiweb\Model\User
     */
    private $participant;

    /**
     * @var \Archiweb\Model\HostedProject
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
     * @return \Archiweb\Model\User
     */
    public function getParticipant () {

        return $this->participant;
    }

    /**
     * Set participant
     *
     * @param \Archiweb\Model\User $participant
     *
     * @return SharedHostedProject
     */
    public function setParticipant (\Archiweb\Model\User $participant) {

        $this->participant = $participant;

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
     * @return SharedHostedProject
     */
    public function setHostedProject (\Archiweb\Model\HostedProject $hostedProject) {

        $this->hostedProject = $hostedProject;

        return $this;
    }
}
