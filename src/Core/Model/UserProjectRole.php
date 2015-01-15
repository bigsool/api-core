<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserProjectRole
 *
 * @ORM\Table(name="userProjectRole", indexes={@ORM\Index(name="hostedProject_id", columns={"hostedProject_id"}), @ORM\Index(name="role_id", columns={"role_id"}), @ORM\Index(name="IDX_6AFB5F0EA76ED395", columns={"user_id"})})
 * @ORM\Entity
 */
class UserProjectRole {

    /**
     * @var \Core\Model\ProjectRole
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Core\Model\ProjectRole", inversedBy="userProjectRoles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="role_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $projectRole;

    /**
     * @var \Core\Model\User
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Core\Model\User", inversedBy="userProjectRoles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $user;

    /**
     * @var \Core\Model\HostedProject
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Core\Model\HostedProject", inversedBy="userProjectRoles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="hostedProject_id", referencedColumnName="id", nullable=false)
     * })
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
