<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 */
class User {

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $firstname;

    /**
     * @var string
     */
    private $lang;

    /**
     * @var string
     */
    private $salt;

    /**
     * @var \DateTime
     */
    private $registerDate;

    /**
     * @var \DateTime
     */
    private $lastLoginDate;

    /**
     * @var string
     */
    private $knowsFrom;

    /**
     * @var string
     */
    private $confirmationKey;

    /**
     * @var \Core\Model\Company
     */
    private $ownedCompany;

    /**
     * @var \Core\Model\Student
     */
    private $student;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $hostedProjects;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $deviceClients;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $projectPersons;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $projectTypicalDescriptions;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $reportTemplates;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $sharedReports;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $systemMessages;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $transactions;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $userProjectRoles;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $sharedHostedProjects;

    /**
     * @var \Core\Model\Company
     */
    private $company;

    /**
     * Constructor
     */
    public function __construct () {

        $this->hostedProjects = new \Doctrine\Common\Collections\ArrayCollection();
        $this->deviceClients = new \Doctrine\Common\Collections\ArrayCollection();
        $this->projectPersons = new \Doctrine\Common\Collections\ArrayCollection();
        $this->projectTypicalDescriptions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->reportTemplates = new \Doctrine\Common\Collections\ArrayCollection();
        $this->sharedReports = new \Doctrine\Common\Collections\ArrayCollection();
        $this->systemMessages = new \Doctrine\Common\Collections\ArrayCollection();
        $this->transactions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->userProjectRoles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->sharedHostedProjects = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId () {

        return $this->id;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail () {

        return $this->email;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail ($email) {

        $this->email = $email;

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
     * @return User
     */
    public function setPassword ($password) {

        $this->password = $password;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName () {

        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return User
     */
    public function setName ($name) {

        $this->name = $name;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname () {

        return $this->firstname;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return User
     */
    public function setFirstname ($firstname) {

        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get lang
     *
     * @return string
     */
    public function getLang () {

        return $this->lang;
    }

    /**
     * Set lang
     *
     * @param string $lang
     *
     * @return User
     */
    public function setLang ($lang) {

        $this->lang = $lang;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt () {

        return $this->salt;
    }

    /**
     * Set salt
     *
     * @param string $salt
     *
     * @return User
     */
    public function setSalt ($salt) {

        $this->salt = $salt;

        return $this;
    }

    /**
     * Get registerDate
     *
     * @return \DateTime
     */
    public function getRegisterDate () {

        return $this->registerDate;
    }

    /**
     * Set registerDate
     *
     * @param \DateTime $registerDate
     *
     * @return User
     */
    public function setRegisterDate ($registerDate) {

        $this->registerDate = $registerDate;

        return $this;
    }

    /**
     * Get lastLoginDate
     *
     * @return \DateTime
     */
    public function getLastLoginDate () {

        return $this->lastLoginDate;
    }

    /**
     * Set lastLoginDate
     *
     * @param \DateTime $lastLoginDate
     *
     * @return User
     */
    public function setLastLoginDate ($lastLoginDate) {

        $this->lastLoginDate = $lastLoginDate;

        return $this;
    }

    /**
     * Get knowsfrom
     *
     * @return string
     */
    public function getKnowsFrom () {

        return $this->knowsFrom;
    }

    /**
     * Set knowsFrom
     *
     * @param string $knowsFrom
     *
     * @return User
     */
    public function setKnowsFrom ($knowsFrom) {

        $this->knowsFrom = $knowsFrom;

        return $this;
    }

    /**
     * Get confirmationkey
     *
     * @return string
     */
    public function getConfirmationKey () {

        return $this->confirmationKey;
    }

    /**
     * Set confirmationkey
     *
     * @param string $confirmationKey
     *
     * @return User
     */
    public function setConfirmationKey ($confirmationKey) {

        $this->confirmationKey = $confirmationKey;

        return $this;
    }

    /**
     * Get ownedCompany
     *
     * @return \Core\Model\Company
     */
    public function getOwnedCompany () {

        return $this->ownedCompany;
    }

    /**
     * Set ownedCompany
     *
     * @param \Core\Model\Company $ownedCompany
     *
     * @return User
     */
    public function setOwnedCompany (\Core\Model\Company $ownedCompany = NULL) {

        $this->ownedCompany = $ownedCompany;

        return $this;
    }

    /**
     * Get student
     *
     * @return \Core\Model\Student
     */
    public function getStudent () {

        return $this->student;
    }

    /**
     * Set student
     *
     * @param \Core\Model\Student $student
     *
     * @return User
     */
    public function setStudent (\Core\Model\Student $student = NULL) {

        $this->student = $student;

        return $this;
    }

    /**
     * Add hostedProjects
     *
     * @param \Core\Model\HostedProject $hostedProjects
     *
     * @return User
     */
    public function addHostedProject (\Core\Model\HostedProject $hostedProjects) {

        $this->hostedProjects[] = $hostedProjects;

        return $this;
    }

    /**
     * Remove hostedProjects
     *
     * @param \Core\Model\HostedProject $hostedProjects
     */
    public function removeHostedProject (\Core\Model\HostedProject $hostedProjects) {

        $this->hostedProjects->removeElement($hostedProjects);
    }

    /**
     * Get hostedProjects
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHostedProjects () {

        return $this->hostedProjects;
    }

    /**
     * Add deviceClients
     *
     * @param \Core\Model\DeviceClient $deviceClients
     *
     * @return User
     */
    public function addDeviceClient (\Core\Model\DeviceClient $deviceClients) {

        $this->deviceClients[] = $deviceClients;

        return $this;
    }

    /**
     * Remove deviceClients
     *
     * @param \Core\Model\DeviceClient $deviceClients
     */
    public function removeDeviceClient (\Core\Model\DeviceClient $deviceClients) {

        $this->deviceClients->removeElement($deviceClients);
    }

    /**
     * Get deviceClients
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDeviceClients () {

        return $this->deviceClients;
    }

    /**
     * Add projectPersons
     *
     * @param \Core\Model\ProjectPerson $projectPersons
     *
     * @return User
     */
    public function addProjectPerson (\Core\Model\ProjectPerson $projectPersons) {

        $this->projectPersons[] = $projectPersons;

        return $this;
    }

    /**
     * Remove projectPersons
     *
     * @param \Core\Model\ProjectPerson $projectPersons
     */
    public function removeProjectPerson (\Core\Model\ProjectPerson $projectPersons) {

        $this->projectPersons->removeElement($projectPersons);
    }

    /**
     * Get projectPersons
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProjectPersons () {

        return $this->projectPersons;
    }

    /**
     * Add projectTypicalDescriptions
     *
     * @param \Core\Model\ProjectTypicalDescriptions $projectTypicalDescriptions
     *
     * @return User
     */
    public function addProjectTypicalDescription (\Core\Model\ProjectTypicalDescriptions $projectTypicalDescriptions) {

        $this->projectTypicalDescriptions[] = $projectTypicalDescriptions;

        return $this;
    }

    /**
     * Remove projectTypicalDescriptions
     *
     * @param \Core\Model\ProjectTypicalDescriptions $projectTypicalDescriptions
     */
    public function removeProjectTypicalDescription (\Core\Model\ProjectTypicalDescriptions $projectTypicalDescriptions) {

        $this->projectTypicalDescriptions->removeElement($projectTypicalDescriptions);
    }

    /**
     * Get projectTypicalDescriptions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProjectTypicalDescriptions () {

        return $this->projectTypicalDescriptions;
    }

    /**
     * Add reportTemplates
     *
     * @param \Core\Model\ReportTemplate $reportTemplates
     *
     * @return User
     */
    public function addReportTemplate (\Core\Model\ReportTemplate $reportTemplates) {

        $this->reportTemplates[] = $reportTemplates;

        return $this;
    }

    /**
     * Remove reportTemplates
     *
     * @param \Core\Model\ReportTemplate $reportTemplates
     */
    public function removeReportTemplate (\Core\Model\ReportTemplate $reportTemplates) {

        $this->reportTemplates->removeElement($reportTemplates);
    }

    /**
     * Get reportTemplates
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReportTemplates () {

        return $this->reportTemplates;
    }

    /**
     * Add sharedReports
     *
     * @param \Core\Model\SharedReport $sharedReports
     *
     * @return User
     */
    public function addSharedReport (\Core\Model\SharedReport $sharedReports) {

        $this->sharedReports[] = $sharedReports;

        return $this;
    }

    /**
     * Remove sharedReports
     *
     * @param \Core\Model\SharedReport $sharedReports
     */
    public function removeSharedReport (\Core\Model\SharedReport $sharedReports) {

        $this->sharedReports->removeElement($sharedReports);
    }

    /**
     * Get sharedReports
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSharedReports () {

        return $this->sharedReports;
    }

    /**
     * Add systemMessages
     *
     * @param \Core\Model\SystemMessage $systemMessages
     *
     * @return User
     */
    public function addSystemMessage (\Core\Model\SystemMessage $systemMessages) {

        $this->systemMessages[] = $systemMessages;

        return $this;
    }

    /**
     * Remove systemMessages
     *
     * @param \Core\Model\SystemMessage $systemMessages
     */
    public function removeSystemMessage (\Core\Model\SystemMessage $systemMessages) {

        $this->systemMessages->removeElement($systemMessages);
    }

    /**
     * Get systemMessages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSystemMessages () {

        return $this->systemMessages;
    }

    /**
     * Add transactions
     *
     * @param \Core\Model\Transaction $transactions
     *
     * @return User
     */
    public function addTransaction (\Core\Model\Transaction $transactions) {

        $this->transactions[] = $transactions;

        return $this;
    }

    /**
     * Remove transactions
     *
     * @param \Core\Model\Transaction $transactions
     */
    public function removeTransaction (\Core\Model\Transaction $transactions) {

        $this->transactions->removeElement($transactions);
    }

    /**
     * Get transactions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTransactions () {

        return $this->transactions;
    }

    /**
     * Add userProjectRoles
     *
     * @param \Core\Model\UserProjectRole $userProjectRoles
     *
     * @return User
     */
    public function addUserProjectRole (\Core\Model\UserProjectRole $userProjectRoles) {

        $this->userProjectRoles[] = $userProjectRoles;

        return $this;
    }

    /**
     * Remove userProjectRoles
     *
     * @param \Core\Model\UserProjectRole $userProjectRoles
     */
    public function removeUserProjectRole (\Core\Model\UserProjectRole $userProjectRoles) {

        $this->userProjectRoles->removeElement($userProjectRoles);
    }

    /**
     * Get userProjectRoles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserProjectRoles () {

        return $this->userProjectRoles;
    }

    /**
     * Add sharedHostedProjects
     *
     * @param \Core\Model\SharedHostedProject $sharedHostedProjects
     *
     * @return User
     */
    public function addSharedHostedProject (\Core\Model\SharedHostedProject $sharedHostedProjects) {

        $this->sharedHostedProjects[] = $sharedHostedProjects;

        return $this;
    }

    /**
     * Remove sharedHostedProjects
     *
     * @param \Core\Model\SharedHostedProject $sharedHostedProjects
     */
    public function removeSharedHostedProject (\Core\Model\SharedHostedProject $sharedHostedProjects) {

        $this->sharedHostedProjects->removeElement($sharedHostedProjects);
    }

    /**
     * Get sharedHostedProjects
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSharedHostedProjects () {

        return $this->sharedHostedProjects;
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
     * @return User
     */
    public function setCompany (\Core\Model\Company $company = NULL) {

        $this->company = $company;

        return $this;
    }
}
