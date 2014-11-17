<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Student
 */
class Student {

    /**
     * @var string
     */
    private $schoolname;

    /**
     * @var string
     */
    private $number;

    /**
     * @var \Core\Model\User
     */
    private $user;

    /**
     * @var \Core\Model\Transaction
     */
    private $licenseTransaction;

    /**
     * Get schoolname
     *
     * @return string
     */
    public function getSchoolname () {

        return $this->schoolname;
    }

    /**
     * Set schoolname
     *
     * @param string $schoolname
     *
     * @return Student
     */
    public function setSchoolname ($schoolname) {

        $this->schoolname = $schoolname;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber () {

        return $this->number;
    }

    /**
     * Set number
     *
     * @param string $number
     *
     * @return Student
     */
    public function setNumber ($number) {

        $this->number = $number;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Core\Model\User
     */
    public function getUser () {

        return $this->user;
    }

    /**
     * Set user
     *
     * @param \Core\Model\User $user
     *
     * @return Student
     */
    public function setUser (\Core\Model\User $user) {

        $this->user = $user;

        return $this;
    }

    /**
     * Get licenseTransaction
     *
     * @return \Core\Model\Transaction
     */
    public function getLicenseTransaction () {

        return $this->licenseTransaction;
    }

    /**
     * Set licenseTransaction
     *
     * @param \Core\Model\Transaction $licenseTransaction
     *
     * @return Student
     */
    public function setLicenseTransaction (\Core\Model\Transaction $licenseTransaction = NULL) {

        $this->licenseTransaction = $licenseTransaction;

        return $this;
    }
}
