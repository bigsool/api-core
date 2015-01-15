<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * SharedHostedProject
 *
 * @ORM\Table(name="sharedHostedProject")
 * @ORM\Entity
 */
class SharedHostedProject
{
    /**
     * @var string
     *
     * @ORM\Column(name="permission", type="string", length=255, nullable=false)
     */
    private $permission;

    /**
     * @var \Core\Model\User
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Core\Model\User", inversedBy="sharedHostedProjects", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="participantId", referencedColumnName="id", nullable=false)
     * })
     */
    private $participant;

    /**
     * @var \Core\Model\HostedProject
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Core\Model\HostedProject", inversedBy="sharedHostedProjects", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="hostedProjectId", referencedColumnName="id", nullable=false)
     * })
     */
    private $hostedProject;


    /**
     * Set permission
     *
     * @param string $permission
     * @return SharedHostedProject
     */
    public function setPermission($permission)
    {
        $this->permission = $permission;

        return $this;
    }

    /**
     * Get permission
     *
     * @return string 
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * Set participant
     *
     * @param \Core\Model\User $participant
     * @return SharedHostedProject
     */
    public function setParticipant(\Core\Model\User $participant)
    {
        $this->participant = $participant;

        return $this;
    }

    /**
     * Get participant
     *
     * @return \Core\Model\User 
     */
    public function getParticipant()
    {
        return $this->participant;
    }

    /**
     * Set hostedProject
     *
     * @param \Core\Model\HostedProject $hostedProject
     * @return SharedHostedProject
     */
    public function setHostedProject(\Core\Model\HostedProject $hostedProject)
    {
        $this->hostedProject = $hostedProject;

        return $this;
    }

    /**
     * Get hostedProject
     *
     * @return \Core\Model\HostedProject 
     */
    public function getHostedProject()
    {
        return $this->hostedProject;
    }
}
