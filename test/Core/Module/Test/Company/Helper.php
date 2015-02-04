<?php


namespace Core\Module\Test\Company;


use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Field\KeyPath;
use Core\Filter\Filter;
use Core\Model\TestCompany;
use Core\Module\BasicHelper;

class Helper extends BasicHelper {

    /**
     * @param ActionContext $actCtx
     * @param array         $params
     */
    public function createTestCompany (ActionContext $actCtx, array $params) {

        $company = new TestCompany();

        $this->basicSave($company, $params);

        $actCtx['testCompany'] = $company;

    }

    /**
     * @param ActionContext $actCtx
     * @param TestCompany   $company
     * @param array         $params
     */
    public function updateTestCompany (ActionContext $actCtx, TestCompany $company, array $params) {

        $this->basicSave($company, $params);

        $actCtx['testCompany'] = $company;

    }

    /**
     * @param ActionContext $actCtx
     * @param KeyPath[]     $keyPaths
     * @param Filter[]      $filters
     * @param bool          $hydrateArray
     */
    public function findTestCompany (ActionContext $actCtx, array $keyPaths = [], array $filters = [],
                                     $hydrateArray = true) {

        $registry = ApplicationContext::getInstance()->getNewRegistry();

        $qryCtx = new FindQueryContext('TestCompany');
        foreach ($keyPaths as $keyPath) {
            $qryCtx->addKeyPath($keyPath);
        }
        foreach ($filters as $filter) {
            $qryCtx->addFilter($filter);
        }

        $actCtx['testCompany'] = $registry->find($qryCtx, $hydrateArray);

    }

} 