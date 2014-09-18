<?php

namespace Archiweb\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * OverconsumptionReminder
 */
class OverconsumptionReminder
{
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
     * @var \Archiweb\Model\Company
     */
    private $companyForWhichThisOverconsumptionReminderIsStillCurrent;

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
     * Set date
     *
     * @param \DateTime $date
     * @return OverconsumptionReminder
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set nextReminderDate
     *
     * @param \DateTime $nextReminderDate
     * @return OverconsumptionReminder
     */
    public function setNextReminderDate($nextReminderDate)
    {
        $this->nextReminderDate = $nextReminderDate;

        return $this;
    }

    /**
     * Get nextReminderDate
     *
     * @return \DateTime 
     */
    public function getNextReminderDate()
    {
        return $this->nextReminderDate;
    }

    /**
     * Set level
     *
     * @param integer $level
     * @return OverconsumptionReminder
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return integer 
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set companyForWhichThisOverconsumptionReminderIsStillCurrent
     *
     * @param \Archiweb\Model\Company $companyForWhichThisOverconsumptionReminderIsStillCurrent
     * @return OverconsumptionReminder
     */
    public function setCompanyForWhichThisOverconsumptionReminderIsStillCurrent(\Archiweb\Model\Company $companyForWhichThisOverconsumptionReminderIsStillCurrent = null)
    {
        $this->companyForWhichThisOverconsumptionReminderIsStillCurrent = $companyForWhichThisOverconsumptionReminderIsStillCurrent;

        return $this;
    }

    /**
     * Get companyForWhichThisOverconsumptionReminderIsStillCurrent
     *
     * @return \Archiweb\Model\Company 
     */
    public function getCompanyForWhichThisOverconsumptionReminderIsStillCurrent()
    {
        return $this->companyForWhichThisOverconsumptionReminderIsStillCurrent;
    }

    /**
     * Set company
     *
     * @param \Archiweb\Model\Company $company
     * @return OverconsumptionReminder
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
