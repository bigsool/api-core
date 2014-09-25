<?php


namespace Archiweb\Context;


use Archiweb\Operation;

interface QueryContext extends ApplicationContextProvider {

    /**
     * @return string
     */
    public function getEntity ();

}