<?php

namespace Core\Functional\Account;


use Core\Functional\WebTestCase;

class CompleteAccountTest extends WebTestCase {

    /**
     * @return array
     */
    public function testCreate () {

        $params = ['email'    => $email = 'test@bigsool.com',
                   'password' => 'qwe',
                   'company'  => ['name' => 'bigsool']
        ];
        $return = $this->get('TestAccount', 'create', $params, 'TestAccount', ['id', 'email', 'password']);
        list($id, $result) = each($return);

        $this->assertSuccess($result, $id);

        $data = $result->result;

        $this->assertInstanceOf('\stdClass', $data);

        $properties = get_object_vars($data);
        $this->assertCount(3, $properties);
        $this->assertArrayHasKey('id', $properties);
        $this->assertArrayHasKey('email', $properties);
        $this->assertArrayHasKey('password', $properties);
        $this->assertArrayHasKey('company', $properties);
        $this->assertInternalType('array', $properties['company']);
        $this->assertArrayHasKey('storage', $properties);
        $this->assertInternalType('array', $properties['storage']);
        $this->assertEquals(1, $properties['id']);
        $this->assertEquals($params['email'], $properties['email']);
        $this->assertRegExp('/^[0-9a-f]{128}$/', $properties['password']);

        $company = $properties['company'];
        $this->assertArrayHasKey('id', $company);
        $this->assertArrayHasKey('name', $company);
        $this->assertEquals($params['company']['name'], $company['name']);
        $this->assertArrayNotHasKey('storage', $company);

        return $properties;

    }

    /**
     * @depends testCreate
     */
    public function testUpdate ($user) {

        $params = ['email'    => $email = 'test@bigsool.com',
                   'password' => 'qwe',
                   'company'  => ['name' => 'bigsool']
        ];
        $return = $this->get('TestAccount', 'create', $params, 'TestAccount', ['id', 'email', 'password']);
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
        $this->assertEquals($params['email'], $properties['email']);
        $this->assertRegExp('/^[0-9a-f]{128}$/', $properties['password']);

    }

}