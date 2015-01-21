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
     * @param Company $company
     */
    public function setCompany (Company $company) {

        $this->getUser()->setCompany($company);
        $company->addUser($this->getUser());

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
     * @param Storage $storage
     */
    public function setStorage (Storage $storage) {

        $this->getCompany()->setStorage($storage);
        $storage->setCompany($this->getCompany());

    }

    /**
     * @return Company
     */
    public function getCompany () {

        return $this->getUser()->getCompany();

    }

    /**
     * @return Storage
     */
    public function getStorage () {

        return $this->getCompany()->getStorage();

    }

}