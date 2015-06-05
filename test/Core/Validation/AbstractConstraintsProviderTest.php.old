<?php


namespace Core\Validation;


use Core\TestCase;
use Core\Validation\Parameter\Email;
use Core\Validation\Parameter\NotBlank;


class AbstractConstraintsProviderTest extends TestCase {

    public static function setUpBeforeClass () {

        parent::setUpBeforeClass();

        // load errors
        self::getApplicationContext();

    }

    public function testGetConstraintsFor () {

        $emailConstraints = [new NotBlank(), new Email()];

        /**
         * @var AbstractConstraintsProvider $provider
         */
        $provider = $this->getMockForAbstractClass('\Core\Validation\AbstractConstraintsProvider');
        $provider->method('listConstraints')->willReturn(['email' => $emailConstraints]);

        $this->assertSame($emailConstraints, $provider->getConstraintsFor('email'));
        $this->assertNull($provider->getConstraintsFor('qwe'));

    }

    public function testValidate () {

        $emailConstraints = [new NotBlank(), new Email()];

        /**
         * @var AbstractConstraintsProvider $provider
         */
        $provider = $this->getMockForAbstractClass('\Core\Validation\AbstractConstraintsProvider');
        $provider->method('listConstraints')->willReturn(['email' => $emailConstraints]);

        $this->assertTrue($provider->validate('julien@bigsool.com', ''));

        $this->assertFalse($provider->validate('', ''));

        $this->assertTrue($provider->validate('', '', true));

        $this->assertFalse($provider->validate('julienbigsool.com', ''));

        $this->assertTrue($provider->validate(123, ''));

    }

} 