<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Functionality
 */
class Functionality {

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $bundleId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var boolean
     */
    private $consumable;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $deviceCompanyStates;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $productFunctionalities;

    /**
     * Constructor
     */
    public function __construct () {

        $this->deviceCompanyStates = new \Doctrine\Common\Collections\ArrayCollection();
        $this->productFunctionalities = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Get bundleId
     *
     * @return string
     */
    public function getBundleId () {

        return $this->bundleId;
    }

    /**
     * Set bundleId
     *
     * @param string $bundleId
     *
     * @return Functionality
     */
    public function setBundleId ($bundleId) {

        $this->bundleId = $bundleId;

        return $this;
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
     * @return Functionality
     */
    public function setName ($name) {

        $this->name = $name;

        return $this;
    }

    /**
     * Get consumable
     *
     * @return boolean
     */
    public function getConsumable () {

        return $this->consumable;
    }

    /**
     * Set consumable
     *
     * @param boolean $consumable
     *
     * @return Functionality
     */
    public function setConsumable ($consumable) {

        $this->consumable = $consumable;

        return $this;
    }

    /**
     * Add deviceCompanyStates
     *
     * @param \Core\Model\DeviceCompanyState $deviceCompanyStates
     *
     * @return Functionality
     */
    public function addDeviceCompanyState (\Core\Model\DeviceCompanyState $deviceCompanyStates) {

        $this->deviceCompanyStates[] = $deviceCompanyStates;

        return $this;
    }

    /**
     * Remove deviceCompanyStates
     *
     * @param \Core\Model\DeviceCompanyState $deviceCompanyStates
     */
    public function removeDeviceCompanyState (\Core\Model\DeviceCompanyState $deviceCompanyStates) {

        $this->deviceCompanyStates->removeElement($deviceCompanyStates);
    }

    /**
     * Get deviceCompanyStates
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDeviceCompanyStates () {

        return $this->deviceCompanyStates;
    }

    /**
     * Add productFunctionalities
     *
     * @param \Core\Model\ProductFunctionality $productFunctionalities
     *
     * @return Functionality
     */
    public function addProductFunctionality (\Core\Model\ProductFunctionality $productFunctionalities) {

        $this->productFunctionalities[] = $productFunctionalities;

        return $this;
    }

    /**
     * Remove productFunctionalities
     *
     * @param \Core\Model\ProductFunctionality $productFunctionalities
     */
    public function removeProductFunctionality (\Core\Model\ProductFunctionality $productFunctionalities) {

        $this->productFunctionalities->removeElement($productFunctionalities);
    }

    /**
     * Get productFunctionalities
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductFunctionalities () {

        return $this->productFunctionalities;
    }
}
