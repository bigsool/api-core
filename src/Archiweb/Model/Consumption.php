<?php

namespace Archiweb\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Consumption
 */
class Consumption
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Archiweb\Model\Functionality
     */
    private $functionality;

    /**
     * @var \Archiweb\Model\Company
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
     * Set functionality
     *
     * @param \Archiweb\Model\Functionality $functionality
     * @return Consumption
     */
    public function setFunctionality(\Archiweb\Model\Functionality $functionality)
    {
        $this->functionality = $functionality;

        return $this;
    }

    /**
     * Get functionality
     *
     * @return \Archiweb\Model\Functionality 
     */
    public function getFunctionality()
    {
        return $this->functionality;
    }

    /**
     * Set company
     *
     * @param \Archiweb\Model\Company $company
     * @return Consumption
     */
    public function setCompany(\Archiweb\Model\Company $company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return \Archiweb\Model\Company 
     */
    public function getCompany()
    {
        return $this->company;
    }
}
