<?php

namespace Archiweb\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * SystemMessage
 */
class SystemMessage
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $localizedmessage;

    /**
     * @var string
     */
    private $type;

    /**
     * @var boolean
     */
    private $sent;

    /**
     * @var \DateTime
     */
    private $creationdate;

    /**
     * @var \DateTime
     */
    private $expirationdate;

    /**
     * @var \Archiweb\Model\User
     */
    private $user;


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
     * Set message
     *
     * @param string $message
     * @return SystemMessage
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set localizedmessage
     *
     * @param string $localizedmessage
     * @return SystemMessage
     */
    public function setLocalizedmessage($localizedmessage)
    {
        $this->localizedmessage = $localizedmessage;

        return $this;
    }

    /**
     * Get localizedmessage
     *
     * @return string 
     */
    public function getLocalizedmessage()
    {
        return $this->localizedmessage;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return SystemMessage
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set sent
     *
     * @param boolean $sent
     * @return SystemMessage
     */
    public function setSent($sent)
    {
        $this->sent = $sent;

        return $this;
    }

    /**
     * Get sent
     *
     * @return boolean 
     */
    public function getSent()
    {
        return $this->sent;
    }

    /**
     * Set creationdate
     *
     * @param \DateTime $creationdate
     * @return SystemMessage
     */
    public function setCreationdate($creationdate)
    {
        $this->creationdate = $creationdate;

        return $this;
    }

    /**
     * Get creationdate
     *
     * @return \DateTime 
     */
    public function getCreationdate()
    {
        return $this->creationdate;
    }

    /**
     * Set expirationdate
     *
     * @param \DateTime $expirationdate
     * @return SystemMessage
     */
    public function setExpirationdate($expirationdate)
    {
        $this->expirationdate = $expirationdate;

        return $this;
    }

    /**
     * Get expirationdate
     *
     * @return \DateTime 
     */
    public function getExpirationdate()
    {
        return $this->expirationdate;
    }

    /**
     * Set user
     *
     * @param \Archiweb\Model\User $user
     * @return SystemMessage
     */
    public function setUser(\Archiweb\Model\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Archiweb\Model\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
