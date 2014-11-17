<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * DeviceClient
 */
class DeviceClient {

    /**
     * @var string
     */
    private $reseller;

    /**
     * @var string
     */
    private $clientName;

    /**
     * @var string
     */
    private $clientVersion;

    /**
     * @var \DateTime
     */
    private $lastLogin;

    /**
     * @var \Core\Model\User
     */
    private $user;

    /**
     * @var \Core\Model\Device
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
