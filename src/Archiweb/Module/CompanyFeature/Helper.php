<?php


namespace Archiweb\Module\CompanyFeature;


use Archiweb\Context\ActionContext;
use Archiweb\Model\Company;
use Archiweb\Parameter\Parameter;

class Helper {

    /**
     * @param ActionContext $actCtx
     * @param Parameter[]   $params
     */
    public function createCompany (ActionContext $actCtx, array $params) {

        $registry = $actCtx->getApplicationContext()->getNewRegistry();

        $company = new Company();

        $company->setName($params['name']);

        $registry->save($company);

        $actCtx['company'] = $company;

    }

} 