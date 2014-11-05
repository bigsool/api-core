<?php


namespace Archiweb\Logger;


trait ExecutionTimer {

    /**
     * @var float
     */
    private $startTime;

    /**
     * @var float
     */
    private $previousTime;

    /**
     * @return string[]
     */
    public function getTimings () {

        if (!$this->startTime) {
            $this->previousTime =
            $this->startTime = isset($_SERVER['REQUEST_TIME_FLOAT']) ? $_SERVER['REQUEST_TIME_FLOAT'] : microtime(true);
        }

        $microtime = microtime(true);
        $totalExecutionTime = number_format($microtime - $this->startTime, 3, '.', '');
        $timeElapsedSincePreviousTrace = number_format($microtime - $this->previousTime, 3, '.', '');
        $this->previousTime = $microtime;

        return [$totalExecutionTime, $timeElapsedSincePreviousTrace];

    }
    
}