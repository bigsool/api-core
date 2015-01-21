<?php

namespace Core\Model;

use Core\Module\MagicalEntity;

class Account extends MagicalEntity {

    /**
     * @var User
     */
    protected $user;

    /**
     * @param User $user
     */
    public function __construct (User $user) {

        $this->user = $user;

    }

    /**
     * @return Company
     */
    public function getCompany () {

        return $this->getUser()->getCompany();

    }

    /**
     * @return User
     */
    public function getUser () {

        return $this->getMainEntity();

    }

    /**
     * @return User
     */
    public function getMainEntity () {

        return $this->user;

    }

    /**
     * @param Company $company
     */
    public function setCompany (Company $company) {

        $this->getUser()->setCompany($company);
        $company->addUser($this->getUser());

    }

    /**
     * @return Storage
     */
    public function getStorage () {

        return $this->getUser()->getCompany()->getStorage();

    }

    /**
     * @param Storage $storage
     */
    public function setStorage (Storage $storage) {

        $this->getUser()->getCompany()->setStorage($storage);
        $storage->setCompany($this->getUser()->getCompany());

    }

}