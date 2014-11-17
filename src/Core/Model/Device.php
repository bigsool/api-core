<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Device
 */
class Device {

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
     * @var \Core\Model\Company
     */
    private $freetrialCompany;

    /**
     * Constructor
     */
    public function __construct () {

        $this->deviceClients = new \Doctrine\Common\Collections\ArrayCollection();
        $this->deviceCompanies = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId () {

        return $this->id;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName () {

        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Device
     */
    public function setName ($name) {

        $this->name = $name;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType () {

        return $this->type;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Device
     */
    public function setType ($type) {

        $this->type = $type;

        return $this;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid () {

        return $this->uuid;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     *
     * @return Device
     */
    public function setUuid ($uuid) {

        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Add deviceClients
     *
     * @param \Core\Model\DeviceClient $deviceClients
     *
     * @return Device
     */
    public function addDeviceClient (\Core\Model\DeviceClient $deviceClients) {

        $this->deviceClients[] = $deviceClients;

        return $this;
    }

    /**
     * Remove deviceClients
     *
     * @param \Core\Model\DeviceClient $deviceClients
     */
    public function removeDeviceClient (\Core\Model\DeviceClient $deviceClients) {

        $this->deviceClients->removeElement($deviceClients);
    }

    /**
     * Get deviceClients
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDeviceClients () {

        return $this->deviceClients;
    }

    /**
     * Add deviceCompanies
     *
     * @param \Core\Model\DeviceCompany $deviceCompanies
     *
     * @return Device
     */
    public function addDeviceCompany (\Core\Model\DeviceCompany $deviceCompanies) {

        $this->deviceCompanies[] = $deviceCompanies;

        return $this;
    }

    /**
     * Remove deviceCompanies
     *
     * @param \Core\Model\DeviceCompany $deviceCompanies
     */
    public function removeDeviceCompany (\Core\Model\DeviceCompany $deviceCompanies) {

        $this->deviceCompanies->removeElement($deviceCompanies);
    }

    /**
     * Get deviceCompanies
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDeviceCompanies () {

        return $this->deviceCompanies;
    }

    /**
     * Get freetrialCompany
     *
     * @return \Core\Model\Company
     */
    public function getFreetrialCompany () {

        return $this->freetrialCompany;
    }

    /**
     * Set freetrialCompany
     *
     * @param \Core\Model\Company $freetrialCompany
     *
     * @return Device
     */
    public function setFreetrialCompany (\Core\Model\Company $freetrialCompany = NULL) {

        $this->freetrialCompany = $freetrialCompany;

        return $this;
    }
}
