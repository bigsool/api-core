<?php

namespace Archiweb\Model;

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
     * @var \Archiweb\Model\Company
     */
    private $company;

    /**
     * @var \Archiweb\Model\Functionality
     */
    private $functionality;

    /**
     * @var \Archiweb\Model\Device
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
     * @return \Archiweb\Model\Company
     */
    public function getCompany () {

        return $this->company;
    }

    /**
     * Set company
     *
     * @param \Archiweb\Model\Company $company
     *
     * @return DeviceCompanyState
     */
    public function setCompany (\Archiweb\Model\Company $company) {

        $this->company = $company;

        return $this;
    }

    /**
     * Get functionality
     *
     * @return \Archiweb\Model\Functionality
     */
    public function getFunctionality () {

        return $this->functionality;
    }

    /**
     * Set functionality
     *
     * @param \Archiweb\Model\Functionality $functionality
     *
     * @return DeviceCompanyState
     */
    public function setFunctionality (\Archiweb\Model\Functionality $functionality) {

        $this->functionality = $functionality;

        return $this;
    }

    /**
     * Get device
     *
     * @return \Archiweb\Model\Device
     */
    public function getDevice () {

        return $this->device;
    }

    /**
     * Set device
     *
     * @param \Archiweb\Model\Device $device
     *
     * @return DeviceCompanyState
     */
    public function setDevice (\Archiweb\Model\Device $device) {

        $this->device = $device;

        return $this;
    }
}
