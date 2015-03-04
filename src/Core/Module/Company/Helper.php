<?php


namespace Core\Module\Company;


use Core\Context\ActionContext;
use Core\Model\Company;
use Core\Module\BasicHelper;

class Helper extends BasicHelper {

    public function createCompany (ActionContext $actionContext, array $params) {

        $company = new Company();

        $this->basicSave($company, $params);

        $actionContext['company'] = $company;

    }

    public function updateCompany (ActionContext $actionContext, Company $company, array $params) {

        $this->basicSave($company, $params);

        $actionContext['company'] = $company;

    }

}