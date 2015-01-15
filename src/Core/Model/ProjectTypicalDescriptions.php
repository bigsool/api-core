<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectTypicalDescriptions
 *
 * @ORM\Table(name="projectTypicalDescriptions")
 * @ORM\Entity
 */
class ProjectTypicalDescriptions
{
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
     * @ORM\Column(name="category", type="string", length=255, nullable=false)
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="descriptions", type="text", nullable=false)
     */
    private $descriptions;

    /**
     * @var \Core\Model\User
     *
     * @ORM\ManyToOne(targetEntity="Core\Model\User", inversedBy="projectTypicalDescriptions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="creator", referencedColumnName="id", nullable=false)
     * })
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
     * @param \Core\Model\User $creator
     * @return ProjectTypicalDescriptions
     */
    public function setCreator(\Core\Model\User $creator)
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * Get creator
     *
     * @return \Core\Model\User 
     */
    public function getCreator()
    {
        return $this->creator;
    }
}
