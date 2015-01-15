<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Functionality
 *
 * @ORM\Table(name="functionality", uniqueConstraints={@ORM\UniqueConstraint(name="functionalityBundleId", columns={"bundleId"})})
 * @ORM\Entity
 */
class Functionality
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="bundleId", type="string", length=255, nullable=false)
     */
    private $bundleId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="consumable", type="boolean", nullable=false)
     */
    private $consumable;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Core\Model\DeviceCompanyState", mappedBy="functionality")
     */
    private $deviceCompanyStates;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Core\Model\ProductFunctionality", mappedBy="functionality")
     */
    private $productFunctionalities;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->deviceCompanyStates = new \Doctrine\Common\Collections\ArrayCollection();
        $this->productFunctionalities = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set bundleId
     *
     * @param string $bundleId
     * @return Functionality
     */
    public function setBundleId($bundleId)
    {
        $this->bundleId = $bundleId;

        return $this;
    }

    /**
     * Get bundleId
     *
     * @return string 
     */
    public function getBundleId()
    {
        return $this->bundleId;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Functionality
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
     * Set consumable
     *
     * @param boolean $consumable
     * @return Functionality
     */
    public function setConsumable($consumable)
    {
        $this->consumable = $consumable;

        return $this;
    }

    /**
     * Get consumable
     *
     * @return boolean 
     */
    public function getConsumable()
    {
        return $this->consumable;
    }

    /**
     * Add deviceCompanyStates
     *
     * @param \Core\Model\DeviceCompanyState $deviceCompanyStates
     * @return Functionality
     */
    public function addDeviceCompanyState(\Core\Model\DeviceCompanyState $deviceCompanyStates)
    {
        $this->deviceCompanyStates[] = $deviceCompanyStates;

        return $this;
    }

    /**
     * Remove deviceCompanyStates
     *
     * @param \Core\Model\DeviceCompanyState $deviceCompanyStates
     */
    public function removeDeviceCompanyState(\Core\Model\DeviceCompanyState $deviceCompanyStates)
    {
        $this->deviceCompanyStates->removeElement($deviceCompanyStates);
    }

    /**
     * Get deviceCompanyStates
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDeviceCompanyStates()
    {
        return $this->deviceCompanyStates;
    }

    /**
     * Add productFunctionalities
     *
     * @param \Core\Model\ProductFunctionality $productFunctionalities
     * @return Functionality
     */
    public function addProductFunctionality(\Core\Model\ProductFunctionality $productFunctionalities)
    {
        $this->productFunctionalities[] = $productFunctionalities;

        return $this;
    }

    /**
     * Remove productFunctionalities
     *
     * @param \Core\Model\ProductFunctionality $productFunctionalities
     */
    public function removeProductFunctionality(\Core\Model\ProductFunctionality $productFunctionalities)
    {
        $this->productFunctionalities->removeElement($productFunctionalities);
    }

    /**
     * Get productFunctionalities
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProductFunctionalities()
    {
        return $this->productFunctionalities;
    }
}
