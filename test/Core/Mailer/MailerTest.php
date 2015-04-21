<?php

namespace Core\Mailer;

use Core\Context\ApplicationContext;
use Core\TestCase;

class MailerTest extends TestCase {

    public function testSend () {

        $appCtx = ApplicationContext::getInstance();
        $result = (new Mailer($appCtx))->send('thierry@bigsool.com','resetYourPassword','you can reset your password by clicking on this link');
        $this->assertInternalType('array', $result);
        $this->assertTrue(count($result) == 1);
        $this->assertSame('thierry@bigsool.com',$result[0]['email']);
        $this->assertSame('sent',$result[0]['status']);
        $this->assertSame(null,$result[0]['reject_reason']);

    }

    public function testSendFromTemplate () {

        $appCtx = ApplicationContext::getInstance();
        $result = (new \Core\Mailer\Mailer($appCtx))->sendFromTemplate('welcome','thierry@bigsool.com','resetYourPassword',['Name' => 'thierry']);
        $this->assertInternalType('array', $result);
        $this->assertTrue(count($result) == 1);
        $this->assertSame('thierry@bigsool.com',$result[0]['email']);
        $this->assertSame('sent',$result[0]['status']);
        $this->assertSame(null,$result[0]['reject_reason']);

    }

}
