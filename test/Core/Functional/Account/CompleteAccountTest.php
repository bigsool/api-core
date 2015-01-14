<?php

namespace Core\Functional\Account;


use Core\Functional\WebTestCase;

class CompleteAccountTest extends WebTestCase {

    public function testCreate () {

        $params = ['email' => $email = 'test@bigsool.com', 'password' => 'qwe',
                   'company' => ['name' => 'bigsool']
        ];
        $return = $this->get('Account', 'create', $params, 'Account', ['id', 'email', 'password']);
        list($id, $result) = each($return);

        $this->assertSuccess($result, $id);

        $data = $result->result;

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

}