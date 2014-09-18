<?php

namespace Archiweb\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * ReportTemplate
 */
class ReportTemplate {

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $data;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var \Archiweb\Model\User
     */
    private $user;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId () {

        return $this->id;
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
     * @return ReportTemplate
     */
    public function setName ($name) {

        $this->name = $name;

        return $this;
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getData () {

        return $this->data;
    }

    /**
     * Set data
     *
     * @param string $data
     *
     * @return ReportTemplate
     */
    public function setData ($data) {

        $this->data = $data;

        return $this;
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
     * @return ReportTemplate
     */
    public function setDate ($date) {

        $this->date = $date;

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
     * @return ReportTemplate
     */
    public function setUser (\Archiweb\Model\User $user) {

        $this->user = $user;

        return $this;
    }
}
