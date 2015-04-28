<?php


namespace Core\Module\Company;


use Core\Context\ActionContext;
use Core\Context\FindQueryContext;
use Core\Field\RelativeField;
use Core\Filter\Filter;
use Core\Module\BasicHelper;

class Helper extends BasicHelper {

    /**
     * @param ActionContext $actionContext
     * @param array         $params
     */
    public function createCompany (ActionContext $actionContext, array $params) {

        $company = $this->createRealModel('Company');

        $this->basicSave($company, $params);

        $actionContext['company'] = $company;

    }

    /**
     * @param ActionContext $actionContext
     * @param               $company
     * @param array         $params
     */
    public function updateCompany (ActionContext $actionContext, $company, array $params) {

        $this->checkRealModelType($company, 'Company');

        $this->basicSave($company, $params);

        $actionContext['company'] = $company;

    }

    /**
     * @param ActionContext   $actionContext
     * @param RelativeField[] $keyPaths
     * @param Filter[]        $filters
     * @param array           $params
     */
    public function findCompany (ActionContext $actionContext, array $keyPaths = [], array $filters = [],
                                 array $params = []) {

        $qryCtx = new FindQueryContext('Company', $actionContext->getRequestContext());

        $actionContext['companies'] = $this->basicFind($qryCtx, $keyPaths, $filters, $params);

    }

}