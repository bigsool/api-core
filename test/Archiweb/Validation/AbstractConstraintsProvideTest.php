<?php


namespace Archiweb\Validation;


use Archiweb\TestCase;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;


class AbstractConstraintsProviderTest extends TestCase {

    public function testGetConstraintsFor () {

        $emailConstraints = [new NotBlank(), new Email()];

        /**
         * @var AbstractConstraintsProvider $provider
         */
        $provider = $this->getMockForAbstractClass('\Archiweb\Validation\AbstractConstraintsProvider');
        $provider->method('listConstraints')->willReturn(['email' => $emailConstraints]);

        $this->assertSame($emailConstraints, $provider->getConstraintsFor('email'));
        $this->assertNull($provider->getConstraintsFor('qwe'));

    }

    public function testValidate () {

        $emailConstraints = [new NotBlank(), new Email()];

        /**
         * @var AbstractConstraintsProvider $provider
         */
        $provider = $this->getMockForAbstractClass('\Archiweb\Validation\AbstractConstraintsProvider');
        $provider->method('listConstraints')->willReturn(['email' => $emailConstraints]);

        $violations = $provider->validate('email', 'julien@bigsool.com');
        $this->assertInstanceOf('\Symfony\Component\Validator\ConstraintViolationListInterface', $violations);
        $this->assertSame(0, $violations->count());

        $violations = $provider->validate('email', 'julienbigsool.com');
        $this->assertInstanceOf('\Symfony\Component\Validator\ConstraintViolationListInterface', $violations);
        $this->assertSame(1, $violations->count());

        $violations = $provider->validate('qwe', 123);
        $this->assertInstanceOf('\Symfony\Component\Validator\ConstraintViolationListInterface', $violations);
        $this->assertSame(0, $violations->count());

    }

} 