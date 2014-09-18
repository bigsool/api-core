<?php

namespace Archiweb\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * SharedReport
 */
class SharedReport {

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $reportid;

    /**
     * @var string
     */
    private $projectid;

    /**
     * @var string
     */
    private $projectname;

    /**
     * @var string
     */
    private $hash;

    /**
     * @var string
     */
    private $password;

    /**
     * @var \DateTime
     */
    private $timestamp;

    /**
     * @var \Archiweb\Model\User
     */
    private $user;

    /**
     * @var \Archiweb\Model\Company
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
     * Get reportid
     *
     * @return string
     */
    public function getReportid () {

        return $this->reportid;
    }

    /**
     * Set reportid
     *
     * @param string $reportid
     *
     * @return SharedReport
     */
    public function setReportid ($reportid) {

        $this->reportid = $reportid;

        return $this;
    }

    /**
     * Get projectid
     *
     * @return string
     */
    public function getProjectid () {

        return $this->projectid;
    }

    /**
     * Set projectid
     *
     * @param string $projectid
     *
     * @return SharedReport
     */
    public function setProjectid ($projectid) {

        $this->projectid = $projectid;

        return $this;
    }

    /**
     * Get projectname
     *
     * @return string
     */
    public function getProjectname () {

        return $this->projectname;
    }

    /**
     * Set projectname
     *
     * @param string $projectname
     *
     * @return SharedReport
     */
    public function setProjectname ($projectname) {

        $this->projectname = $projectname;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash () {

        return $this->hash;
    }

    /**
     * Set hash
     *
     * @param string $hash
     *
     * @return SharedReport
     */
    public function setHash ($hash) {

        $this->hash = $hash;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword () {

        return $this->password;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return SharedReport
     */
    public function setPassword ($password) {

        $this->password = $password;

        return $this;
    }

    /**
     * Get timestamp
     *
     * @return \DateTime
     */
    public function getTimestamp () {

        return $this->timestamp;
    }

    /**
     * Set timestamp
     *
     * @param \DateTime $timestamp
     *
     * @return SharedReport
     */
    public function setTimestamp ($timestamp) {

        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Archiweb\Model\User
     */
    public function getUser () {

        return $this->user;
    }

    /**
     * Set user
     *
     * @param \Archiweb\Model\User $user
     *
     * @return SharedReport
     */
    public function setUser (\Archiweb\Model\User $user) {

        $this->user = $user;

        return $this;
    }

    /**
     * Get company
     *
     * @return \Archiweb\Model\Company
     */
    public function getCompany () {

        return $this->company;
    }

    /**
     * Set company
     *
     * @param \Archiweb\Model\Company $company
     *
     * @return SharedReport
     */
    public function setCompany (\Archiweb\Model\Company $company) {

        $this->company = $company;

        return $this;
    }
}
