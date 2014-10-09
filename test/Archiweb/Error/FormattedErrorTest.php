<?php


namespace Archiweb\Error;

use Archiweb\TestCase;
use Archiweb\Error\Error;
use Archiweb\Error\formattedError;

class FormattedErrorTest extends TestCase {


    private $error;
    private $formattedError;

    public function setUp () {

        parent::setUp();
        $this->error = new Error(201,"Id utilisateur invalide","User id invalid","userId",200);
        $this->formattedError = new FormattedError($this->error);

    }

}