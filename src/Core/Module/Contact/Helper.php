<?php


namespace Core\Module\Contact;

use Core\Context\ActionContext;
use Core\Helper\BasicHelper;
use Core\Model\Address;
use Core\Model\Email;
use Core\Model\Phone;

class Helper extends BasicHelper {

    /**
     * @param ActionContext $actCtx
     * @param array         $params
     */
    public function createContact (ActionContext $actCtx, array $params) {

        $contact = $this->createRealModel('Contact');

        $this->basicSetValues($contact, $params);

        $actCtx['contact'] = $contact;

    }

    /**
     * @param ActionContext $actCtx
     * @param               $contact
     * @param array         $params
     */
    public function updateContact (ActionContext $actCtx, $contact, array $params) {

        $this->checkRealModelType($contact, 'Contact');

        $this->basicSetValues($contact, $params);

        $actCtx['contact'] = $contact;

    }

} 