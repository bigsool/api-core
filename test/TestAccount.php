<?php

namespace Core\Model;

use Core\Module\MagicalEntity;

class TestAccount extends MagicalEntity {

    /**
     * @var TestUser
     */
    protected $user;

    /**
     * @param TestUser $user
     */
    public function __construct (TestUser $user) {

        $this->user = $user;

    }

    /**
     * @return TestCompany
     */
    public function getCompany () {

        return $this->getUser()->getCompany();

    }

    /**
     * @return TestUser
     */
    public function getUser () {

        return $this->getMainEntity();

    }

    /**
     * @return TestUser
     */
    public function getMainEntity () {

        return $this->user;

    }

    /**
     * @param TestCompany $company
     */
    public function setCompany (TestCompany $company) {

        $this->getUser()->setCompany($company);
        $company->addUser($this->getUser());

    }

    /**
     * @return TestStorage
     */
    public function getCompanyStorage () {

        return $this->getUser()->getCompany()->getStorage();

    }

    /**
     * @param TestStorage $storage
     */
    public function setCompanyStorage (TestStorage $storage) {

        $this->getUser()->getCompany()->setStorage($storage);
        $storage->setCompany($this->getUser()->getCompany());

    }

    /**
     * @return TestStorage
     */
    public function getStorage () {

        return $this->getUser()->getStorage();

    }

    /**
     * @param TestStorage $storage
     */
    public function setStorage (TestStorage $storage) {

        $this->getUser()->setStorage($storage);
        $storage->setUser($this->getUser());

    }

}