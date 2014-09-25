<?php


namespace Archiweb\Context;


use Archiweb\Field;
use Archiweb\Filter\Filter;
use Archiweb\Operation;

interface QueryContext extends ApplicationContextProvider {

    /**
     * @return string
     */
    public function getEntity ();

}