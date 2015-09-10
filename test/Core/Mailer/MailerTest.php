<?php

namespace Core\Mailer;

use Core\Context\ApplicationContext;
use Core\PHPUnit\Constraint\InArray;
use Core\TestCase;

class MailerTest extends TestCase {

    public static function setUpBeforeClass () {

        parent::setUpBeforeClass();

        static::getApplicationContext();

    }

    public function testSend () {

        $appCtx = ApplicationContext::getInstance();
        $result =
            (new Mailer($appCtx))->send('thierry@bigsool.com', 'resetYourPassword',
                                        'you can reset your password by clicking on this link');
        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);
        $this->assertArrayHasKey(0,$result);
        $this->assertInternalType('array', $result[0]);
        $this->assertArrayHasKey('email', $result[0]);
        $this->assertSame('thierry@bigsool.com', $result[0]['email']);
        $this->assertArrayHasKey('status', $result[0]);
        $this->assertThat($result[0]['status'], new InArray(['sent', 'queued']));
        $this->assertArrayHasKey('reject_reason', $result[0]);
        $this->assertNull($result[0]['reject_reason']);

    }

    public function testSendFromTemplate () {

        $appCtx = ApplicationContext::getInstance();
        $result =
            (new \Core\Mailer\Mailer($appCtx))->sendFromTemplate('test', 'thierry@bigsool.com',
                                                                 ['Name' => 'thierry'], 'resetYourPassword');
        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);
        $this->assertArrayHasKey(0,$result);
        $this->assertInternalType('array', $result[0]);
        $this->assertArrayHasKey('email', $result[0]);
        $this->assertSame('thierry@bigsool.com', $result[0]['email']);
        $this->assertArrayHasKey('status', $result[0]);
        $this->assertThat($result[0]['status'], new InArray(['sent', 'queued']));
        $this->assertArrayHasKey('reject_reason', $result[0]);
        $this->assertNull($result[0]['reject_reason']);

    }

}
