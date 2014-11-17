<?php


namespace Core\Module\CompanyFeature;


use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Model\Company;
use Core\Parameter\Parameter;

class Helper {

    /**
     * @param ActionContext $actCtx
     * @param Parameter[]   $params
     */
    public function createCompany (ActionContext $actCtx, array $params) {

        $registry = ApplicationContext::getInstance()->getNewRegistry();

        $company = new Company();

        $company->setName($params['name']);

        $registry->save($company);

        $actCtx['company'] = $company;

    }

} 