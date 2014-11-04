<?php


namespace Archiweb\Functional\User\Create;


use Archiweb\Functional\WebTestCase;

class UserWithCompanyTestCase extends WebTestCase {

    public function testMinimalData () {

        $result = $this->get('user', 'create', ['user' => ['email' => 'test@bigsool.com', 'password' => 'qwe'],
                                      'company' => ['name' => 'bigsool']
            ],'User',['id','email','password']);

        $this->assertInstanceOf('\stdClass', $result);

        $properties = get_object_vars($result);
        $this->assertCount(3, $properties);
        $this->assertArrayHasKey('id', $properties);
        $this->assertArrayHasKey('email', $properties);
        $this->assertArrayHasKey('password', $properties);
        $this->assertEquals(1, $result->id);
        $this->assertEquals('julien@bigsool.com', $result->email);
        $this->assertRegExp('/^[0-9a-f]{128}$/', $result->password);

    }

} 