<?php

namespace Archiweb\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Device
 */
class Device
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $uuid;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $deviceClients;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $deviceCompanies;

    /**
     * @var \Archiweb\Model\Company
     */
    private $freetrialCompany;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->deviceClients = new \Doctrine\Common\Collections\ArrayCollection();
        $this->deviceCompanies = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Device
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Device
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     * @return Device
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid
     *
     * @return string 
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Add deviceClients
     *
     * @param \Archiweb\Model\DeviceClient $deviceClients
     * @return Device
     */
    public function addDeviceClient(\Archiweb\Model\DeviceClient $deviceClients)
    {
        $this->deviceClients[] = $deviceClients;

        return $this;
    }

    /**
     * Remove deviceClients
     *
     * @param \Archiweb\Model\DeviceClient $deviceClients
     */
    public function removeDeviceClient(\Archiweb\Model\DeviceClient $deviceClients)
    {
        $this->deviceClients->removeElement($deviceClients);
    }

    /**
     * Get deviceClients
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDeviceClients()
    {
        return $this->deviceClients;
    }

    /**
     * Add deviceCompanies
     *
     * @param \Archiweb\Model\DeviceCompany $deviceCompanies
     * @return Device
     */
    public function addDeviceCompany(\Archiweb\Model\DeviceCompany $deviceCompanies)
    {
        $this->deviceCompanies[] = $deviceCompanies;

        return $this;
    }

    /**
     * Remove deviceCompanies
     *
     * @param \Archiweb\Model\DeviceCompany $deviceCompanies
     */
    public function removeDeviceCompany(\Archiweb\Model\DeviceCompany $deviceCompanies)
    {
        $this->deviceCompanies->removeElement($deviceCompanies);
    }

    /**
     * Get deviceCompanies
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDeviceCompanies()
    {
        return $this->deviceCompanies;
    }

    /**
     * Set freetrialCompany
     *
     * @param \Archiweb\Model\Company $freetrialCompany
     * @return Device
     */
    public function setFreetrialCompany(\Archiweb\Model\Company $freetrialCompany = null)
    {
        $this->freetrialCompany = $freetrialCompany;

        return $this;
    }

    /**
     * Get freetrialCompany
     *
     * @return \Archiweb\Model\Company 
     */
    public function getFreetrialCompany()
    {
        return $this->freetrialCompany;
    }
}
