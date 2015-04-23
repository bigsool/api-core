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
     * @return TestCompany
     */
    public function getUnrestrictedCompany () {

        return $this->getUser()->getUnrestrictedCompany();

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
     * @return TestStorage
     */
    public function getUnrestrictedCompanyStorage () {

        return $this->getUser()->getUnrestrictedCompany()->getUnrestrictedStorage();

    }

    /**
     * @param TestStorage $storage
     */
    public function setCompanyStorage (TestStorage $storage) {

        $this->getUser()->getUnrestrictedCompany()->setStorage($storage);
        $storage->setCompany($this->getUser()->getUnrestrictedCompany());

    }

    /**
     * @return TestStorage
     */
    public function getStorage () {

        return $this->getUser()->getStorage();

    }

    /**
     * @return TestStorage
     */
    public function getUnrestrictedStorage () {

        return $this->getUser()->getUnrestrictedStorage();

    }

    /**
     * @param TestStorage $storage
     */
    public function setStorage (TestStorage $storage) {

        $this->getUser()->setStorage($storage);
        $storage->setUser($this->getUser());

    }

}