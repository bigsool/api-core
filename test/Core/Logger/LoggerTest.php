<?php
/**
 * Created by PhpStorm.
 * User: Julien
 * Date: 19/12/2014
 * Time: 15:13
 */

namespace Core\Logger;


use Core\TestCase;

class LoggerTest extends TestCase {

    public function testSession () {

        $sessionId = uniqid('', true);
        $logger1 = new Logger();
        $logger1->setSessionId($sessionId);
        $this->assertSame($sessionId, $logger1->getSessionId());

        $this->assertNotEmpty((new Logger())->getSessionId());

        $this->assertNotEquals((new Logger())->getSessionId(), (new Logger())->getSessionId());

    }

    public function testFormat () {

        $logger = new Logger();
        $format = '%message% %context% %session_id% [%datetime%] %level_name%:\n';
        $logger->setFormat($format);
        $this->assertSame(str_replace('%session_id%', $logger->getSessionId(), $format), $logger->getFormat());

    }

    public function testChannel () {

        $logger = new Logger();
        $this->assertSame('log', $logger->getChannel());

    }

}