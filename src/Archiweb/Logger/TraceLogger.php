<?php

namespace Archiweb\Logger;


class TraceLogger extends AbstractLogger {

    use ExecutionTimer;

    public function trace ($message = '') {

        $backtrace = debug_backtrace();
        list($totalExecutionTime, $timeSincePreviousTrace) = $this->getTimings();

        $this->getMLogger()->addDebug($totalExecutionTime . 's | ' . $timeSincePreviousTrace . 's | ' . $message,
                                      $backtrace[1]);

    }

    /**
     * @return string
     */
    public function getChannel () {

        return 'trace';

    }
}