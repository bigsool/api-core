<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * DeviceCompany
 */
class DeviceCompany {

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Core\Model\Device
     */
    private $device;

    /**
     * @var \Core\Model\Company
     */
    private $company;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId () {

        return $this->id;
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
     * @return DeviceCompany
     */
    public function setDevice (\Core\Model\Device $device) {

        $this->device = $device;

        return $this;
    }

    /**
     * Get company
     *
     * @return \Core\Model\Company
     */
    public function getCompany () {

        return $this->company;
    }

    /**
     * Set company
     *
     * @param \Core\Model\Company $company
     *
     * @return DeviceCompany
     */
    public function setCompany (\Core\Model\Company $company) {

        $this->company = $company;

        return $this;
    }
}
