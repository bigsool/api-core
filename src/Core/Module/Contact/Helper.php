<?php


namespace Core\Module\Contact;

use Core\Context\ActionContext;
use Core\Model\Address;
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
     * @param               $contact
     * @param array         $params
     */
    public function updateContact (ActionContext $actCtx, $contact, array $params) {

        $this->checkRealModelType($contact, 'Contact');

        $this->basicSave($contact, $params);

        $actCtx['contact'] = $contact;

    }

} 