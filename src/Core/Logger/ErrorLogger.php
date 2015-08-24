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
     * @var callable
     */
    protected $shutdownFunction;

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

                    http_response_code(500);
                    die(sprintf('Internal Error, please contact us at support@archipad with the error number %s.',
                                $this->getSessionId()));

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

                $this->getMLogger()->addError('Uncaught ' . strval($e));

                http_response_code(500);
                die(sprintf('Internal Error, please contact us at support@archipad with the error number %s.',
                            $this->getSessionId()));

            };

        }

        return $this->exceptionHandler;

    }

    /**
     * @return callable
     */
    public function getShutdownFunction () {

        if (!isset($this->shutdownFunction)) {

            $this->shutdownFunction = function () {

                $lastError = error_get_last();

                if (is_array($lastError)) {

                    $type = isset($lastError['type']) ? $lastError['type'] : '-';
                    $message = isset($lastError['message']) ? $lastError['message'] : '-';
                    $file = isset($lastError['file']) ? $lastError['file'] : '-';
                    $line = isset($lastError['line']) ? $lastError['line'] : '-';

                    $this->getMLogger()->addError("Shutdown with error $type: $message ($file : $line)");

                    http_response_code(500);
                    die(sprintf('Internal Error, please contact us at support@archipad with the error number %s.',
                                $this->getSessionId()));

                }

            };

        }

        return $this->shutdownFunction;

    }

}