<?php


namespace Core\Module\AddressBook;


use Core\Context\ActionContext;
use Core\Module\BasicHelper;

class Helper extends BasicHelper {

    /**
     * @param ActionContext $actCtx
     * @param array         $params
     */
    public function createABCompany (ActionContext $actCtx, array $params) {

        $entity = $this->createRealModel('ABCompany');

        $this->basicSave($entity, $params);

        $actCtx['abcompany'] = $entity;

    }

    /**
     * @param ActionContext $actCtx
     * @param array         $params
     */
    public function createABPerson (ActionContext $actCtx, array $params) {

        $entity = $this->createRealModel('ABPerson');

        $this->basicSave($entity, $params);

        $actCtx['abperson'] = $entity;

    }

    /**
     * @param ActionContext $actCtx
     * @param array         $params
     */
    public function createABCompanyContact (ActionContext $actCtx, array $params) {

        $entity = $this->createRealModel('ABCompanyContact');

        $this->basicSave($entity, $params);

        $actCtx['abcompanyContact'] = $entity;

    }

    /**
     * @param ActionContext $actCtx
     * @param array         $params
     */
    public function createABPersonContact (ActionContext $actCtx, array $params) {

        $entity = $this->createRealModel('ABPersonContact');

        $this->basicSave($entity, $params);

        $actCtx['abpersonContact'] = $entity;

    }

}