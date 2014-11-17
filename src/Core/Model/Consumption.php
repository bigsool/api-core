<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Consumption
 */
class Consumption {

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Core\Model\Functionality
     */
    private $functionality;

    /**
     * @var \Core\Model\Company
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
