<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * DeviceCompanyState
 *
 * @ORM\Table(name="deviceCompanyState", indexes={@ORM\Index(name="IDX_4816AACA39EDDC8", columns={"functionality_id"})})
 * @ORM\Entity
 */
class DeviceCompanyState {

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     */
    private $enabled;

    /**
     * @var \Core\Model\Company
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Core\Model\Company", inversedBy="deviceCompanyStates")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $company;

    /**
     * @var \Core\Model\Functionality
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Core\Model\Functionality", inversedBy="deviceCompanyStates")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="functionality_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $functionality;

    /**
     * @var \Core\Model\Device
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Core\Model\Device", inversedBy="deviceCompanyStates")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="device_id", referencedColumnName="id", nullable=false)
     * })
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
