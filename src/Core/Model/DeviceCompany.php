<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * DeviceCompany
 *
 * @ORM\Table(name="deviceCompany", uniqueConstraints={@ORM\UniqueConstraint(name="device_id", columns={"device_id", "company_id"})}, indexes={@ORM\Index(name="IDX_AA21A64B94A4C7D4", columns={"device_id"})})
 * @ORM\Entity
 */
class DeviceCompany
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
     * @var \Core\Model\Device
     *
     * @ORM\ManyToOne(targetEntity="Core\Model\Device", inversedBy="deviceCompanies")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="device_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $device;

    /**
     * @var \Core\Model\Company
     *
     * @ORM\ManyToOne(targetEntity="Core\Model\Company", inversedBy="deviceCompanies")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $company;


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
     * Set device
     *
     * @param \Core\Model\Device $device
     * @return DeviceCompany
     */
    public function setDevice(\Core\Model\Device $device)
    {
        $this->device = $device;

        return $this;
    }

    /**
     * Get device
     *
     * @return \Core\Model\Device 
     */
    public function getDevice()
    {
        return $this->device;
    }

    /**
     * Set company
     *
     * @param \Core\Model\Company $company
     * @return DeviceCompany
     */
    public function setCompany(\Core\Model\Company $company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return \Core\Model\Company 
     */
    public function getCompany()
    {
        return $this->company;
    }
}
