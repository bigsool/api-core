<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Company
 *
 * @ORM\Table(name="company")
 * @ORM\Entity
 */
class Company {

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
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="vat", type="string", length=255, nullable=true)
     */
    private $vat;

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
     * @return Company
     */
    public function setName ($name) {

        $this->name = $name;

        return $this;
    }

    /**
     * Get vat
     *
     * @return string
     */
    public function getVat () {

        return $this->vat;
    }

    /**
     * Set vat
     *
     * @param string $vat
     *
     * @return Company
     */
    public function setVat ($vat) {

        $this->vat = $vat;

        return $this;
    }
}
