<?php

namespace Archiweb\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * DeviceClient
 */
class DeviceClient
{
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
     * @var \Archiweb\Model\User
     */
    private $user;

    /**
     * @var \Archiweb\Model\Device
     */
    private $device;


    /**
     * Set reseller
     *
     * @param string $reseller
     * @return DeviceClient
     */
    public function setReseller($reseller)
    {
        $this->reseller = $reseller;

        return $this;
    }

    /**
     * Get reseller
     *
     * @return string 
     */
    public function getReseller()
    {
        return $this->reseller;
    }

    /**
     * Set clientName
     *
     * @param string $clientName
     * @return DeviceClient
     */
    public function setClientName($clientName)
    {
        $this->clientName = $clientName;

        return $this;
    }

    /**
     * Get clientName
     *
     * @return string 
     */
    public function getClientName()
    {
        return $this->clientName;
    }

    /**
     * Set clientVersion
     *
     * @param string $clientVersion
     * @return DeviceClient
     */
    public function setClientVersion($clientVersion)
    {
        $this->clientVersion = $clientVersion;

        return $this;
    }

    /**
     * Get clientVersion
     *
     * @return string 
     */
    public function getClientVersion()
    {
        return $this->clientVersion;
    }

    /**
     * Set lastLogin
     *
     * @param \DateTime $lastLogin
     * @return DeviceClient
     */
    public function setLastLogin($lastLogin)
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * Get lastLogin
     *
     * @return \DateTime 
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Set user
     *
     * @param \Archiweb\Model\User $user
     * @return DeviceClient
     */
    public function setUser(\Archiweb\Model\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Archiweb\Model\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set device
     *
     * @param \Archiweb\Model\Device $device
     * @return DeviceClient
     */
    public function setDevice(\Archiweb\Model\Device $device)
    {
        $this->device = $device;

        return $this;
    }

    /**
     * Get device
     *
     * @return \Archiweb\Model\Device 
     */
    public function getDevice()
    {
        return $this->device;
    }
}
