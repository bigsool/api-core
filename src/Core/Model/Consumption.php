<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Consumption
 *
 * @ORM\Table(name="consumption", indexes={@ORM\Index(name="product_id", columns={"functionality_id"}), @ORM\Index(name="company_id", columns={"company_id"})})
 * @ORM\Entity
 */
class Consumption {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Core\Model\Functionality
     *
     * @ORM\ManyToOne(targetEntity="Core\Model\Functionality", inversedBy="consumptions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="functionality_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $functionality;

    /**
     * @var \Core\Model\Company
     *
     * @ORM\ManyToOne(targetEntity="Core\Model\Company", inversedBy="consumptions")
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
    public function getId () {

        return $this->id;
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
     * @return Consumption
     */
    public function setFunctionality (\Core\Model\Functionality $functionality) {

        $this->functionality = $functionality;

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
     * @return Consumption
     */
    public function setCompany (\Core\Model\Company $company) {

        $this->company = $company;

        return $this;
    }
}
