<?php


namespace Archiweb\Context;


interface ApplicationContextProvider {

    /**
     * @return ApplicationContext
     */
    public function getApplicationContext ();

} 