<?php

namespace Archiweb\Filter;

use Archiweb\Context\ApplicationContext;
use Archiweb\Expression\Expression;

class FilterReference extends Filter {

    private $appCtx;

    /**
     * @param ApplicationContext $appCtx
     * @param string             $entity
     * @param string             $name
     */
    function __construct (ApplicationContext $appCtx, $entity, $name) {

        $this->appCtx = $appCtx;
        parent::__construct($entity, $name, NULL);

    }

    /**
     * @return Expression
     * @throws \Exception
     */
    public function getExpression () {

        $filters = $this->appCtx->getFilters();

        foreach ($filters as $filter) {

            if ($filter->getEntity() == $this->getEntity() && $filter->getName() == $this->getName()) {
                return $filter->getExpression();
            }

        }

        throw new \Exception('Reference a no existing filter !');

    }

}
