<?php


namespace Core\Module\Company;


use Core\Context\ActionContext;
use Core\Module\BasicHelper;

class Helper extends BasicHelper {

    public function createCompany (ActionContext $actionContext, array $params) {

        $company = $this->createRealModel('Company');

        $this->basicSave($company, $params);

        $actionContext['company'] = $company;

    }

    public function updateCompany (ActionContext $actionContext, $company, array $params) {

        $this->checkRealModelType($company, 'Company');

        $this->basicSave($company, $params);

        $actionContext['company'] = $company;

    }

}