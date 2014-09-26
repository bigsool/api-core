<?php


namespace Archiweb\Context;


interface QueryContext extends ApplicationContextProvider {

    /**
     * @return string
     */
    public function getEntity ();

}