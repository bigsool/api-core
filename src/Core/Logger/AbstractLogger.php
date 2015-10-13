<?php


namespace Core\Logger;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger as MLogger;

abstract class AbstractLogger implements LoggerInterface {

    /**
     * @var MLogger
     */
    protected $mLogger;

    /**
     * @var string
     */
    protected $format = "[%datetime%] %session_id% %level_name%: %message% %context%\n";

    /**
     * @var string
     */
    protected $dateFormat = 'H:i:s';

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @return MLogger
     */
    public function getMLogger () {

        if (!isset($this->mLogger)) {
            $channel = $this->getChannel();
            $this->mLogger = new MLogger($channel);

            // TODO
            /*
            if ($config['environment'] <= ENV_DEV) $level = MLogger::DEBUG;
            elseif ($config['environment'] <= ENV_STAGE) $level = MLogger::NOTICE;
            else $level = MLogger::WARNING;
            */
            $level = MLogger::DEBUG;

            date_default_timezone_set('Europe/Paris');

            $stream = new RotatingFileHandler(ROOT_DIR . '/../logs/' . $channel . '.log', 0, $level);
            $stream->setFormatter(new LineFormatter($this->getFormat(), $this->getDateFormat()));
            $this->mLogger->pushHandler($stream);
        }

        return $this->mLogger;

    }

    /**
     * @return string
     */
    public function getDateFormat () {

        return $this->dateFormat;

    }

    /**
     * @param string $dateFormat
     */
    public function setDateFormat ($dateFormat) {

        $this->dateFormat = $dateFormat;

    }

    /**
     * @return string
     */
    public function getFormat () {

        return str_replace('%session_id%', $this->getSessionId(), $this->format);

    }

    /**
     * @param string $format
     */
    public function setFormat ($format) {

        $this->format = $format;

    }

    /**
     * @return string
     */
    public function getSessionId () {

        if (!isset($this->sessionId)) {
            $this->setSessionId(uniqid());
        }

        return $this->sessionId;

    }

    /**
     * @param string $sessionId
     */
    public function setSessionId ($sessionId) {

        $this->sessionId = $sessionId;

    }

} 