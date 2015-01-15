<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Device
 *
 * @ORM\Table(name="device", indexes={@ORM\Index(name="freetrial_company_id", columns={"freetrial_company_id"})})
 * @ORM\Entity
 */
class Device
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
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=255, nullable=false)
     */
    private $uuid;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Core\Model\DeviceClient", mappedBy="device")
     */
    private $deviceClients;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Core\Model\DeviceCompany", mappedBy="device")
     */
    private $deviceCompanies;

    /**
     * @var \Core\Model\Company
     *
     * @ORM\ManyToOne(targetEntity="Core\Model\Company", inversedBy="devicesUsedForTheFreetrial")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="freetrial_company_id", referencedColumnName="id")
     * })
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
     * @param \Core\Model\DeviceClient $deviceClients
     * @return Device
     */
    public function addDeviceClient(\Core\Model\DeviceClient $deviceClients)
    {
        $this->deviceClients[] = $deviceClients;

        return $this;
    }

    /**
     * Remove deviceClients
     *
     * @param \Core\Model\DeviceClient $deviceClients
     */
    public function removeDeviceClient(\Core\Model\DeviceClient $deviceClients)
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
     * @param \Core\Model\DeviceCompany $deviceCompanies
     * @return Device
     */
    public function addDeviceCompany(\Core\Model\DeviceCompany $deviceCompanies)
    {
        $this->deviceCompanies[] = $deviceCompanies;

        return $this;
    }

    /**
     * Remove deviceCompanies
     *
     * @param \Core\Model\DeviceCompany $deviceCompanies
     */
    public function removeDeviceCompany(\Core\Model\DeviceCompany $deviceCompanies)
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
     * @param \Core\Model\Company $freetrialCompany
     * @return Device
     */
    public function setFreetrialCompany(\Core\Model\Company $freetrialCompany = null)
    {
        $this->freetrialCompany = $freetrialCompany;

        return $this;
    }

    /**
     * Get freetrialCompany
     *
     * @return \Core\Model\Company 
     */
    public function getFreetrialCompany()
    {
        return $this->freetrialCompany;
    }
}
