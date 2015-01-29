<?php


namespace Core\Logger;


class ErrorLogger extends AbstractLogger {

    /**
     * @var callable
     */
    protected $errorHandler;

    /**
     * @var callable
     */
    protected $exceptionHandler;

    /**
     * @return string
     */
    public function getChannel () {

        return 'errors';

    }

    /**
     * @return callable
     */
    public function getErrorHandler () {

        if (!isset($this->errorHandler)) {

            $this->errorHandler =
                function ($errno, $errstr, $errfile = NULL, $errline = NULL, array $errcontext = NULL) {

                    $jsonCtx = json_encode($errcontext);
                    $this->getMLogger()->addError("[$errno] $errstr ($errfile : $errline) [$jsonCtx]");

                };

        }

        return $this->errorHandler;

    }

    /**
     * @return callable
     */
    public function getExceptionHandler () {

        if (!isset($this->exceptionHandler)) {

            $this->exceptionHandler = function (\Exception $e) {

                $this->getMLogger()->addError('Uncaught '.strval($e));

            };

        }

        return $this->exceptionHandler;

    }

}