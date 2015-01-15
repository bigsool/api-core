<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * DeviceClient
 *
 * @ORM\Table(name="deviceClient", indexes={@ORM\Index(name="user_id", columns={"user_id"}), @ORM\Index(name="IDX_8C550D9894A4C7D4", columns={"device_id"})})
 * @ORM\Entity
 */
class DeviceClient {

    /**
     * @var string
     *
     * @ORM\Column(name="reseller", type="string", length=255)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $reseller;

    /**
     * @var string
     *
     * @ORM\Column(name="clientName", type="string", length=255)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $clientName;

    /**
     * @var string
     *
     * @ORM\Column(name="clientVersion", type="string", length=255)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $clientVersion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastLogin", type="datetime", nullable=false)
     */
    private $lastLogin;

    /**
     * @var \Core\Model\User
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Core\Model\User", inversedBy="deviceClients")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $user;

    /**
     * @var \Core\Model\Device
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Core\Model\Device", inversedBy="deviceClients")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="device_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $device;

    /**
     * Get reseller
     *
     * @return string
     */
    public function getReseller () {

        return $this->reseller;
    }

    /**
     * Set reseller
     *
     * @param string $reseller
     *
     * @return DeviceClient
     */
    public function setReseller ($reseller) {

        $this->reseller = $reseller;

        return $this;
    }

    /**
     * Get clientName
     *
     * @return string
     */
    public function getClientName () {

        return $this->clientName;
    }

    /**
     * Set clientName
     *
     * @param string $clientName
     *
     * @return DeviceClient
     */
    public function setClientName ($clientName) {

        $this->clientName = $clientName;

        return $this;
    }

    /**
     * Get clientVersion
     *
     * @return string
     */
    public function getClientVersion () {

        return $this->clientVersion;
    }

    /**
     * Set clientVersion
     *
     * @param string $clientVersion
     *
     * @return DeviceClient
     */
    public function setClientVersion ($clientVersion) {

        $this->clientVersion = $clientVersion;

        return $this;
    }

    /**
     * Get lastLogin
     *
     * @return \DateTime
     */
    public function getLastLogin () {

        return $this->lastLogin;
    }

    /**
     * Set lastLogin
     *
     * @param \DateTime $lastLogin
     *
     * @return DeviceClient
     */
    public function setLastLogin ($lastLogin) {

        $this->lastLogin = $lastLogin;

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
     * @return DeviceClient
     */
    public function setUser (\Core\Model\User $user) {

        $this->user = $user;

        return $this;
    }

    /**
     * Get device
     *
     * @return \Core\Model\Device
     */
    public function getDevice () {

        return $this->device;
    }

    /**
     * Set device
     *
     * @param \Core\Model\Device $device
     *
     * @return DeviceClient
     */
    public function setDevice (\Core\Model\Device $device) {

        $this->device = $device;

        return $this;
    }
}
