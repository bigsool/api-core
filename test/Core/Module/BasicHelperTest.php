<?php


namespace Core\Module;


use Core\Helper\BasicHelper;
use Core\Model\TestUser;
use Core\Registry;
use Core\TestCase;

class BasicHelperTest extends TestCase {

    public static function setUpBeforeClass () {

        parent::setUpBeforeClass();

        $ctx = self::getApplicationContext();

        self::resetDatabase($ctx);

    }

    public function testBasicSetValues () {

        $basicHelper = new BasicHelper($this->getApplicationContext());

        $user = $basicHelper->basicSetValues(new TestUser(), [
            'email'        => 'qweqwe@qweqwe.com',
            'password'     => 'OhMyQweqwe',
            'name'         => 'AzeEn',
            'firstname'    => 'QweFr',
            'lang'         => 'fr',
            'salt'         => uniqid('', true),
            'registerDate' => new \DateTime(),
        ]);

        $this->assertInstanceOf(Registry::realModelClassName('testUser'), $user);
        $this->assertNull($user->getId());

    }

    /**
     * @expectedException \Exception
     */
    public function testBasicSaveOnNotObject () {

        (new BasicHelper($this->getApplicationContext()))->basicSetValues('qwe', []);

    }

    /**
     * @expectedException \Exception
     */
    public function testBasicSaveWithWrongParameter () {

        (new BasicHelper($this->getApplicationContext()))->basicSetValues(new TestUser(), ['qwe' => 'qwe']);

    }

    public function testRealModelClassName () {

        $this->assertSame('\Core\Model\TestUser',
                          (new BasicHelper($this->getApplicationContext()))->getRealModelClassName('TestUser'));
        $this->assertInstanceOf('\Core\Model\TestUser',
                                (new BasicHelper($this->getApplicationContext()))->createRealModel('TestUser'));

    }

    public function testCheckRealModelType () {

        $this->assertNull((new BasicHelper($this->getApplicationContext()))->checkRealModelType(new TestUser(),
                                                                                                'TestUser'));

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidRealModelType () {

        (new BasicHelper($this->getApplicationContext()))->checkRealModelType(new \stdClass(), 'TestUser');

    }

} 