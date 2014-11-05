<?php


namespace Archiweb\Functional\User\Create;


use Archiweb\Functional\WebTestCase;

class UserWithCompanyTestCase extends WebTestCase {

    public function testMinimalData () {

        $params = ['user'    => ['email' => $email = 'test@bigsool.com', 'password' => 'qwe'],
                   'company' => ['name' => 'bigsool']
        ];
        $result = $this->get('user', 'create', $params, 'User', ['id', 'email', 'password']);

        $this->assertSuccess($result);

        $data = $result->data;

        $this->assertInstanceOf('\stdClass', $data);

        $properties = get_object_vars($data);
        $this->assertCount(3, $properties);
        $this->assertArrayHasKey('id', $properties);
        $this->assertArrayHasKey('email', $properties);
        $this->assertArrayHasKey('password', $properties);
        $this->assertEquals(1, $properties['id']);
        $this->assertEquals($params['user']['email'], $properties['email']);
        $this->assertRegExp('/^[0-9a-f]{128}$/', $properties['password']);

    }

    public function testUserNotFound () {

        $params = ['company' => ['name' => 'bigsool']];
        $result = $this->get('user', 'create', $params, 'User', ['id', 'email', 'password']);

        $this->assertFail($result, 100, [], 'user');

    }

    public function testCompanyNotFound () {

        $params = ['user' => ['email' => $email = 'test@bigsool.com', 'password' => 'qwe']];
        $result = $this->get('user', 'create', $params, 'User', ['id', 'email', 'password']);

        $this->assertFail($result, 100, [], 'company');

    }

} 