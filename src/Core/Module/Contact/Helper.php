<?php


namespace Core\Module\Contact;

use Core\Context\ActionContext;
use Core\Model\Address;
use Core\Model\Contact;
use Core\Model\Email;
use Core\Model\Phone;
use Core\Module\BasicHelper;

class Helper extends BasicHelper {

    public function createContact (ActionContext $actCtx, array $params) {

        $contact = new Contact();

        $this->basicSave($contact, $params);

        $actCtx['contact'] = $contact;

    }

    public function createPhone (ActionContext $actCtx, array $params) {

        $phone = new Phone();

        $this->basicSave($phone, $params);

        $actCtx['phone'] = $phone;

    }

    public function createAddress (ActionContext $actCtx, array $params) {

        $address = new Address();

        $this->basicSave($address, $params);

        $actCtx['address'] = $address;

    }

    public function createEmail (ActionContext $actCtx, array $params) {

        $email = new Email();

        $this->basicSave($email, $params);

        $actCtx['email'] = $email;

    }

} 