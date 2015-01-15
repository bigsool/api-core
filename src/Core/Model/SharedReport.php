<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * SharedReport
 *
 * @ORM\Table(name="sharedReport", uniqueConstraints={@ORM\UniqueConstraint(name="report", columns={"reportId", "projectId"})})
 * @ORM\Entity
 */
class SharedReport {

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
     * @ORM\Column(name="reportId", type="string", length=255, nullable=false)
     */
    private $reportid;

    /**
     * @var string
     *
     * @ORM\Column(name="projectId", type="string", length=16, nullable=false)
     */
    private $projectid;

    /**
     * @var string
     *
     * @ORM\Column(name="projectName", type="string", length=255, nullable=false)
     */
    private $projectname;

    /**
     * @var string
     *
     * @ORM\Column(name="hash", type="string", length=255, nullable=false)
     */
    private $hash;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    private $timestamp;

    /**
     * @var \Core\Model\User
     *
     * @ORM\ManyToOne(targetEntity="Core\Model\User", inversedBy="sharedReports")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $user;

    /**
     * @var \Core\Model\Company
     *
     * @ORM\ManyToOne(targetEntity="Core\Model\Company", inversedBy="sharedReports")
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
     * @return SharedReport
     */
    public function setUser (\Core\Model\User $user) {

        $this->user = $user;

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
     * @return SharedReport
     */
    public function setCompany (\Core\Model\Company $company) {

        $this->company = $company;

        return $this;
    }
}
