<?php

namespace Core\Logger;

class SQLLogger extends AbstractLogger implements \Doctrine\DBAL\Logging\SQLLogger {

    use ExecutionTimer;

    public function __construct () {

        $this->setDateFormat('H:i:s:u');

    }

    /**
     * @param string $sql
     * @param array  $params
     * @param array  $types
     */
    public function startQuery ($sql, array $params = NULL, array $types = NULL) {

        list($totalExecutionTime, $timeSincePreviousTrace) = $this->getTimings();

        $this->getMLogger()->addInfo($totalExecutionTime . 's | ' . $timeSincePreviousTrace . 's | ' . $sql,
                                     array('params' => $params, 'types' => $types));

    }

    /**
     *
     */
    public function stopQuery () {

        list($totalExecutionTime, $timeSincePreviousTrace) = $this->getTimings();

        $this->getMLogger()->addInfo($totalExecutionTime . 's | ' . $timeSincePreviousTrace . 's | done');

    }

    /**
     * @return string
     */
    public function getChannel () {

        return 'sql';

    }
}