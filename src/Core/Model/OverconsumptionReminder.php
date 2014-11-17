<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * OverconsumptionReminder
 */
class OverconsumptionReminder {

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var \DateTime
     */
    private $nextReminderDate;

    /**
     * @var integer
     */
    private $level;

    /**
     * @var \Core\Model\Company
     */
    private $companyForWhichThisOverconsumptionReminderIsStillCurrent;

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
     * Get date
     *
     * @return \DateTime
     */
    public function getDate () {

        return $this->date;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return OverconsumptionReminder
     */
    public function setDate ($date) {

        $this->date = $date;

        return $this;
    }

    /**
     * Get nextReminderDate
     *
     * @return \DateTime
     */
    public function getNextReminderDate () {

        return $this->nextReminderDate;
    }

    /**
     * Set nextReminderDate
     *
     * @param \DateTime $nextReminderDate
     *
     * @return OverconsumptionReminder
     */
    public function setNextReminderDate ($nextReminderDate) {

        $this->nextReminderDate = $nextReminderDate;

        return $this;
    }

    /**
     * Get level
     *
     * @return integer
     */
    public function getLevel () {

        return $this->level;
    }

    /**
     * Set level
     *
     * @param integer $level
     *
     * @return OverconsumptionReminder
     */
    public function setLevel ($level) {

        $this->level = $level;

        return $this;
    }

    /**
     * Get companyForWhichThisOverconsumptionReminderIsStillCurrent
     *
     * @return \Core\Model\Company
     */
    public function getCompanyForWhichThisOverconsumptionReminderIsStillCurrent () {

        return $this->companyForWhichThisOverconsumptionReminderIsStillCurrent;
    }

    /**
     * Set companyForWhichThisOverconsumptionReminderIsStillCurrent
     *
     * @param \Core\Model\Company $companyForWhichThisOverconsumptionReminderIsStillCurrent
     *
     * @return OverconsumptionReminder
     */
    public function setCompanyForWhichThisOverconsumptionReminderIsStillCurrent (\Core\Model\Company $companyForWhichThisOverconsumptionReminderIsStillCurrent = NULL) {

        $this->companyForWhichThisOverconsumptionReminderIsStillCurrent =
            $companyForWhichThisOverconsumptionReminderIsStillCurrent;

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
     * @return OverconsumptionReminder
     */
    public function setCompany (\Core\Model\Company $company) {

        $this->company = $company;

        return $this;
    }
}
