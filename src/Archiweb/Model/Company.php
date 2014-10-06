<?php

namespace Archiweb\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Company
 */
class Company {

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
    private $address;

    /**
     * @var string
     */
    private $zipCode;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $state;

    /**
     * @var string
     */
    private $country;

    /**
     * @var string
     */
    private $tel;

    /**
     * @var string
     */
    private $fax;

    /**
     * @var string
     */
    private $tva;

    /**
     * @var \Archiweb\Model\OverconsumptionReminder
     */
    private $currentOverconsumptionReminder;

    /**
     * @var \Archiweb\Model\User
     */
    private $owner;

    /**
     * @var \Archiweb\Model\Storage
     */
    private $storage;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $overconsumptionReminders;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $deviceCompanies;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $devicesUsedForTheFreetrial;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $users;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $sharedReports;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $defaultRoles;

    /**
     * Constructor
     */
    public function __construct () {

        $this->overconsumptionReminders = new \Doctrine\Common\Collections\ArrayCollection();
        $this->deviceCompanies = new \Doctrine\Common\Collections\ArrayCollection();
        $this->devicesUsedForTheFreetrial = new \Doctrine\Common\Collections\ArrayCollection();
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->sharedReports = new \Doctrine\Common\Collections\ArrayCollection();
        $this->defaultRoles = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Company
     */
    public function setName ($name) {

        $this->name = $name;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress () {

        return $this->address;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Company
     */
    public function setAddress ($address) {

        $this->address = $address;

        return $this;
    }

    /**
     * Get zipCode
     *
     * @return string
     */
    public function getZipCode () {

        return $this->zipCode;
    }

    /**
     * Set zipCode
     *
     * @param string $zipCode
     *
     * @return Company
     */
    public function setZipCode ($zipCode) {

        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity () {

        return $this->city;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Company
     */
    public function setCity ($city) {

        $this->city = $city;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState () {

        return $this->state;
    }

    /**
     * Set state
     *
     * @param string $state
     *
     * @return Company
     */
    public function setState ($state) {

        $this->state = $state;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry () {

        return $this->country;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return Company
     */
    public function setCountry ($country) {

        $this->country = $country;

        return $this;
    }

    /**
     * Get tel
     *
     * @return string
     */
    public function getTel () {

        return $this->tel;
    }

    /**
     * Set tel
     *
     * @param string $tel
     *
     * @return Company
     */
    public function setTel ($tel) {

        $this->tel = $tel;

        return $this;
    }

    /**
     * Get fax
     *
     * @return string
     */
    public function getFax () {

        return $this->fax;
    }

    /**
     * Set fax
     *
     * @param string $fax
     *
     * @return Company
     */
    public function setFax ($fax) {

        $this->fax = $fax;

        return $this;
    }

    /**
     * Get tva
     *
     * @return string
     */
    public function getTva () {

        return $this->tva;
    }

    /**
     * Set tva
     *
     * @param string $tva
     *
     * @return Company
     */
    public function setTva ($tva) {

        $this->tva = $tva;

        return $this;
    }

    /**
     * Get currentOverconsumptionReminder
     *
     * @return \Archiweb\Model\OverconsumptionReminder
     */
    public function getCurrentOverconsumptionReminder () {

        return $this->currentOverconsumptionReminder;
    }

    /**
     * Set currentOverconsumptionReminder
     *
     * @param \Archiweb\Model\OverconsumptionReminder $currentOverconsumptionReminder
     *
     * @return Company
     */
    public function setCurrentOverconsumptionReminder (\Archiweb\Model\OverconsumptionReminder $currentOverconsumptionReminder = NULL) {

        $this->currentOverconsumptionReminder = $currentOverconsumptionReminder;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \Archiweb\Model\User
     */
    public function getOwner () {

        return $this->owner;
    }

    /**
     * Set owner
     *
     * @param \Archiweb\Model\User $owner
     *
     * @return Company
     */
    public function setOwner (\Archiweb\Model\User $owner = NULL) {

        $this->owner = $owner;

        return $this;
    }

    /**
     * Get storage
     *
     * @return \Archiweb\Model\Storage
     */
    public function getStorage () {

        return $this->storage;
    }

    /**
     * Set storage
     *
     * @param \Archiweb\Model\Storage $storage
     *
     * @return Company
     */
    public function setStorage (\Archiweb\Model\Storage $storage = NULL) {

        $this->storage = $storage;

        return $this;
    }

    /**
     * Add overconsumptionReminders
     *
     * @param \Archiweb\Model\OverconsumptionReminder $overconsumptionReminders
     *
     * @return Company
     */
    public function addOverconsumptionReminder (\Archiweb\Model\OverconsumptionReminder $overconsumptionReminders) {

        $this->overconsumptionReminders[] = $overconsumptionReminders;

        return $this;
    }

    /**
     * Remove overconsumptionReminders
     *
     * @param \Archiweb\Model\OverconsumptionReminder $overconsumptionReminders
     */
    public function removeOverconsumptionReminder (\Archiweb\Model\OverconsumptionReminder $overconsumptionReminders) {

        $this->overconsumptionReminders->removeElement($overconsumptionReminders);
    }

    /**
     * Get overconsumptionReminders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOverconsumptionReminders () {

        return $this->overconsumptionReminders;
    }

    /**
     * Add deviceCompanies
     *
     * @param \Archiweb\Model\DeviceCompany $deviceCompanies
     *
     * @return Company
     */
    public function addDeviceCompany (\Archiweb\Model\DeviceCompany $deviceCompanies) {

        $this->deviceCompanies[] = $deviceCompanies;

        return $this;
    }

    /**
     * Remove deviceCompanies
     *
     * @param \Archiweb\Model\DeviceCompany $deviceCompanies
     */
    public function removeDeviceCompany (\Archiweb\Model\DeviceCompany $deviceCompanies) {

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
     * Add devicesUsedForTheFreetrial
     *
     * @param \Archiweb\Model\Device $devicesUsedForTheFreetrial
     *
     * @return Company
     */
    public function addDevicesUsedForTheFreetrial (\Archiweb\Model\Device $devicesUsedForTheFreetrial) {

        $this->devicesUsedForTheFreetrial[] = $devicesUsedForTheFreetrial;

        return $this;
    }

    /**
     * Remove devicesUsedForTheFreetrial
     *
     * @param \Archiweb\Model\Device $devicesUsedForTheFreetrial
     */
    public function removeDevicesUsedForTheFreetrial (\Archiweb\Model\Device $devicesUsedForTheFreetrial) {

        $this->devicesUsedForTheFreetrial->removeElement($devicesUsedForTheFreetrial);
    }

    /**
     * Get devicesUsedForTheFreetrial
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDevicesUsedForTheFreetrial () {

        return $this->devicesUsedForTheFreetrial;
    }

    /**
     * Add users
     *
     * @param \Archiweb\Model\User $users
     *
     * @return Company
     */
    public function addUser (\Archiweb\Model\User $users) {

        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \Archiweb\Model\User $users
     */
    public function removeUser (\Archiweb\Model\User $users) {

        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers () {

        return $this->users;
    }

    /**
     * Add sharedReports
     *
     * @param \Archiweb\Model\SharedReport $sharedReports
     *
     * @return Company
     */
    public function addSharedReport (\Archiweb\Model\SharedReport $sharedReports) {

        $this->sharedReports[] = $sharedReports;

        return $this;
    }

    /**
     * Remove sharedReports
     *
     * @param \Archiweb\Model\SharedReport $sharedReports
     */
    public function removeSharedReport (\Archiweb\Model\SharedReport $sharedReports) {

        $this->sharedReports->removeElement($sharedReports);
    }

    /**
     * Get sharedReports
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSharedReports () {

        return $this->sharedReports;
    }

    /**
     * Add defaultRoles
     *
     * @param \Archiweb\Model\ProjectRole $defaultRoles
     *
     * @return Company
     */
    public function addDefaultRole (\Archiweb\Model\ProjectRole $defaultRoles) {

        $this->defaultRoles[] = $defaultRoles;

        return $this;
    }

    /**
     * Remove defaultRoles
     *
     * @param \Archiweb\Model\ProjectRole $defaultRoles
     */
    public function removeDefaultRole (\Archiweb\Model\ProjectRole $defaultRoles) {

        $this->defaultRoles->removeElement($defaultRoles);
    }

    /**
     * Get defaultRoles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDefaultRoles () {

        return $this->defaultRoles;
    }
}
