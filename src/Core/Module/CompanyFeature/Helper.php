<?php


namespace Core\Module\CompanyFeature;


use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Field\KeyPath;
use Core\Filter\Filter;
use Core\Model\Company;
use Core\Module\BasicHelper;

class Helper extends BasicHelper {

    /**
     * @param ActionContext $actCtx
     * @param array         $params
     */
    public function createCompany (ActionContext $actCtx, array $params) {

        $company = new Company();

        $this->basicSave($company, $params);

        $actCtx['company'] = $company;

    }

    /**
     * @param ActionContext $actCtx
     * @param Company       $company
     * @param array         $params
     */
    public function updateCompany (ActionContext $actCtx, Company $company, array $params) {

        $this->basicSave($company, $params);

        $actCtx['company'] = $company;

    }

    /**
     * @param ActionContext $actCtx
     * @param KeyPath[]     $keyPaths
     * @param Filter[]      $filters
     * @param bool          $hydrateArray
     */
    public function findCompany (ActionContext $actCtx, array $keyPaths = [], array $filters = [],
                                 $hydrateArray = true) {

        $registry = ApplicationContext::getInstance()->getNewRegistry();

        $qryCtx = new FindQueryContext('Company');
        foreach ($keyPaths as $keyPath) {
            $qryCtx->addKeyPath($keyPath);
        }
        foreach ($filters as $filter) {
            $qryCtx->addFilter($filter);
        }

        $actCtx['company'] = $registry->find($qryCtx, $hydrateArray);

    }

} 