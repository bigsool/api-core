<?php

namespace Archiweb\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Student
 */
class Student
{
    /**
     * @var string
     */
    private $schoolname;

    /**
     * @var string
     */
    private $number;

    /**
     * @var \Archiweb\Model\User
     */
    private $user;

    /**
     * @var \Archiweb\Model\Transaction
     */
    private $licenseTransaction;


    /**
     * Set schoolname
     *
     * @param string $schoolname
     * @return Student
     */
    public function setSchoolname($schoolname)
    {
        $this->schoolname = $schoolname;

        return $this;
    }

    /**
     * Get schoolname
     *
     * @return string 
     */
    public function getSchoolname()
    {
        return $this->schoolname;
    }

    /**
     * Set number
     *
     * @param string $number
     * @return Student
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set user
     *
     * @param \Archiweb\Model\User $user
     * @return Student
     */
    public function setUser(\Archiweb\Model\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Archiweb\Model\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set licenseTransaction
     *
     * @param \Archiweb\Model\Transaction $licenseTransaction
     * @return Student
     */
    public function setLicenseTransaction(\Archiweb\Model\Transaction $licenseTransaction = null)
    {
        $this->licenseTransaction = $licenseTransaction;

        return $this;
    }

    /**
     * Get licenseTransaction
     *
     * @return \Archiweb\Model\Transaction 
     */
    public function getLicenseTransaction()
    {
        return $this->licenseTransaction;
    }
}
