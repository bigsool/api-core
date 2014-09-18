<?php

namespace Archiweb\Model;

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
     * @var \Archiweb\Model\Device
     */
    private $device;

    /**
     * @var \Archiweb\Model\Company
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
     * @return DeviceCompany
     */
    public function setDevice (\Archiweb\Model\Device $device) {

        $this->device = $device;

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
     * @return DeviceCompany
     */
    public function setCompany (\Archiweb\Model\Company $company) {

        $this->company = $company;

        return $this;
    }
}
