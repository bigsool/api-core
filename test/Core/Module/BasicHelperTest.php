<?php


namespace Core\Module;


use Core\Model\TestUser;
use Core\Registry;
use Core\TestCase;

class BasicHelperTest extends TestCase {

    public static function setUpBeforeClass () {

        parent::setUpBeforeClass();

        $ctx = self::getApplicationContext();

        self::resetDatabase($ctx);

    }

    public function testCreate () {

        $basicHelper = new BasicHelper();

        $user = $basicHelper->basicSave(new TestUser(), [
            'email'        => 'qweqwe@qweqwe.com',
            'password'     => 'OhMyQweqwe',
            'name'         => 'AzeEn',
            'firstname'    => 'QweFr',
            'lang'         => 'fr',
            'salt'         => uniqid('', true),
            'registerDate' => new \DateTime(),
        ], false);

        $this->assertInstanceOf(Registry::realModelClassName('testUser'), $user);
        $this->assertNull($user->getId());

        $user = $basicHelper->basicSave(new TestUser, [
            'email'        => 'qweqwe2@qweqwe.com',
            'password'     => 'OhMy2ndQwe',
            'name'         => 'QsdEn',
            'firstname'    => 'WxcFr',
            'lang'         => 'en',
            'salt'         => uniqid('', true),
            'registerDate' => new \DateTime(),
        ]);

        $this->assertInstanceOf(Registry::realModelClassName('testUser'), $user);
        $this->assertSame(1, $user->getId());

    }

    /**
     * @expectedException \Exception
     */
    public function testBasicSaveOnNotObject () {

        (new BasicHelper())->basicSave('qwe', []);

    }

    /**
     * @expectedException \Exception
     */
    public function testBasicSaveWithWrongParameter () {

        (new BasicHelper())->basicSave(new TestUser(), ['qwe' => 'qwe']);

    }

    public function testRealModelClassName () {

        $this->assertSame('\Core\Model\TestUser', (new BasicHelper())->getRealModelClassName('TestUser'));
        $this->assertInstanceOf('\Core\Model\TestUser', (new BasicHelper())->createRealModel('TestUser'));

    }

    public function testCheckRealModelType () {

        $this->assertNull((new BasicHelper())->checkRealModelType(new TestUser(),'TestUser'));

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidRealModelType () {

        (new BasicHelper())->checkRealModelType(new \stdClass(),'TestUser');

    }

} 