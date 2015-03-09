<?php


namespace Core\Module\Contact;

use Core\Context\ActionContext;
use Core\Model\Address;
use Core\Model\Contact;
use Core\Model\Email;
use Core\Model\Phone;
use Core\Module\BasicHelper;

class Helper extends BasicHelper {

    /**
     * @param ActionContext $actCtx
     * @param array         $params
     */
    public function createContact (ActionContext $actCtx, array $params) {

        $contact = $this->createRealModel('Contact');

        $this->basicSave($contact, $params);

        $actCtx['contact'] = $contact;

    }

    /**
     * @param ActionContext $actCtx
     * @param array         $params
     */
    public function createPhone (ActionContext $actCtx, array $params) {

        $phone = $this->createRealModel('Phone');

        $this->basicSave($phone, $params);

        $actCtx['phone'] = $phone;

    }

    /**
     * @param ActionContext $actCtx
     * @param array         $params
     */
    public function createAddress (ActionContext $actCtx, array $params) {

        $address = $this->createRealModel('Address');

        $this->basicSave($address, $params);

        $actCtx['address'] = $address;

    }

    /**
     * @param ActionContext $actCtx
     * @param array         $params
     */
    public function createEmail (ActionContext $actCtx, array $params) {

        $email = $this->createRealModel('Email');

        $this->basicSave($email, $params);

        $actCtx['email'] = $email;

    }

} 