<?php


namespace Core\Logger;


interface LoggerInterface {

    /**
     * @return string
     */
    public function getChannel ();

    /**
     * @param string $format
     */
    public function setFormat ($format);

    /**
     * @return string
     */
    public function getFormat ();

    /**
     * @param string $dateFormat
     */
    public function setDateFormat ($dateFormat);

    /**
     * @return string
     */
    public function getDateFormat ();

    /**
     * @return string
     */
    public function getSessionId ();

    /**
     * @param string $sessionId
     */
    public function setSessionId ($sessionId);

} 