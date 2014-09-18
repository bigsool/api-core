<?php

namespace Archiweb\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectTypicalDescriptions
 */
class ProjectTypicalDescriptions
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $category;

    /**
     * @var string
     */
    private $descriptions;

    /**
     * @var \Archiweb\Model\User
     */
    private $creator;


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
     * Set category
     *
     * @param string $category
     * @return ProjectTypicalDescriptions
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set descriptions
     *
     * @param string $descriptions
     * @return ProjectTypicalDescriptions
     */
    public function setDescriptions($descriptions)
    {
        $this->descriptions = $descriptions;

        return $this;
    }

    /**
     * Get descriptions
     *
     * @return string 
     */
    public function getDescriptions()
    {
        return $this->descriptions;
    }

    /**
     * Set creator
     *
     * @param \Archiweb\Model\User $creator
     * @return ProjectTypicalDescriptions
     */
    public function setCreator(\Archiweb\Model\User $creator)
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * Get creator
     *
     * @return \Archiweb\Model\User 
     */
    public function getCreator()
    {
        return $this->creator;
    }
}
