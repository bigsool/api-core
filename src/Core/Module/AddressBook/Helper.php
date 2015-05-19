<?php


namespace Core\Module\AddressBook;


use Core\Context\ActionContext;
use Core\Helper\BasicHelper;

class Helper extends BasicHelper {

    /**
     * @param ActionContext $actCtx
     * @param array         $params
     */
    public function createABCompany (ActionContext $actCtx, array $params) {

        $entity = $this->createRealModel('ABCompany');

        $this->basicSetValues($entity, $params);

        $actCtx['abcompany'] = $entity;

    }

    /**
     * @param ActionContext $actCtx
     * @param array         $params
     */
    public function createABPerson (ActionContext $actCtx, array $params) {

        $entity = $this->createRealModel('ABPerson');

        $this->basicSetValues($entity, $params);

        $actCtx['abperson'] = $entity;

    }

    /**
     * @param ActionContext $actCtx
     * @param array         $params
     */
    public function createABCompanyContact (ActionContext $actCtx, array $params) {

        $entity = $this->createRealModel('ABCompanyContact');

        $this->basicSetValues($entity, $params);

        $actCtx['abcompanyContact'] = $entity;

    }

    /**
     * @param ActionContext $actCtx
     * @param array         $params
     */
    public function createABPersonContact (ActionContext $actCtx, array $params) {

        $entity = $this->createRealModel('ABPersonContact');

        $this->basicSetValues($entity, $params);

        $actCtx['abpersonContact'] = $entity;

    }

}