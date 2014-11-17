<?php

namespace Core\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Admin
 */
class Admin {

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $email;

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
     * @return Admin
     */
    public function setEmail ($email) {

        $this->email = $email;

        return $this;
    }
}
