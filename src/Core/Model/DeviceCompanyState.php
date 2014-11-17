<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * DeviceCompanyState
 */
class DeviceCompanyState {

    /**
     * @var boolean
     */
    private $enabled;

    /**
     * @var \Core\Model\Company
     */
    private $company;

    /**
     * @var \Core\Model\Functionality
     */
    private $functionality;

    /**
     * @var \Core\Model\Device
     */
    private $device;

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled () {

        return $this->enabled;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return DeviceCompanyState
     */
    public function setEnabled ($enabled) {

        $this->enabled = $enabled;

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
     * @return DeviceCompanyState
     */
    public function setCompany (\Core\Model\Company $company) {

        $this->company = $company;

        return $this;
    }

    /**
     * Get functionality
     *
     * @return \Core\Model\Functionality
     */
    public function getFunctionality () {

        return $this->functionality;
    }

    /**
     * Set functionality
     *
     * @param \Core\Model\Functionality $functionality
     *
     * @return DeviceCompanyState
     */
    public function setFunctionality (\Core\Model\Functionality $functionality) {

        $this->functionality = $functionality;

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
     * @return DeviceCompanyState
     */
    public function setDevice (\Core\Model\Device $device) {

        $this->device = $device;

        return $this;
    }
}
