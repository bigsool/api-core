<?php


namespace Core\Field;

use Core\Context\FindQueryContext;
use Core\Filter\StringFilter;
use Core\Model\TestCompany;
use Core\Model\TestCredential;
use Core\Model\TestLoginHistory;
use Core\Model\TestStorage;
use Core\Model\TestUser;
use Core\Module\DbModuleEntity;
use Core\Module\TestUser\TestUserDefinition;
use Core\TestCase;

class AggregateTest extends TestCase {

    /**
     * @var TestUser
     */
    protected static $user;

    /**
     * @var DbModuleEntity
     */
    protected static $testUserEntity;

    /**
     * @var \DateTime[]
     */
    protected static $loginDates = [];

    public static function setUpBeforeClass () {

        parent::setUpBeforeClass();

        self::resetDatabase(self::getApplicationContext());

        self::$user = new TestUser();
        self::$user->setLang('fr');
        self::$user->setCreationDate(new \DateTime());

        $credential = new TestCredential();
        $credential->setType('password');
        $credential->setLogin(uniqid('login'));
        $credential->setPassword(sha1(uniqid()));

        self::$user->setCredential($credential);
        $credential->setUser(self::$user);

        foreach (['now', '-1 hour', '-1 day'] as $date) {

            $loginHistory = new TestLoginHistory();
            self::$loginDates[] = $dateTime = new \DateTime($date);
            $loginHistory->setDate($dateTime);
            $loginHistory->setIP(rand(1, 254) . '.' . rand(1, 254) . '.' . rand(1, 254) . '.' . rand(1, 254));
            $loginHistory->setCredential($credential);
            $credential->addLoginHistory($loginHistory);

        }

        $registry = self::getRegistry();
        $registry->save(self::$user);

        self::$testUserEntity = new DbModuleEntity(self::getApplicationContext(),new TestUserDefinition());

    }

    /**
     *
     */
    public function testResolve () {

        $qryCtx = new FindQueryContext('TestUser');
        $qryCtx->addField(new RelativeField(new Aggregate('COUNT', '*')), 'theNbUsers');
        $qryCtx->addField('*');
        $qryCtx->addFilter(new StringFilter('TestUser','','id = :id'), self::$user->getId());

        $result = self::$testUserEntity->find($qryCtx);

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);
        $this->assertSame("1", $result[0]['theNbUsers']);


        $qryCtx = new FindQueryContext('TestUser');
        $qryCtx->addField(new RelativeField(new Aggregate('MAX', 'credential.loginHistories.date')), 'theLastLoginDate');
        $qryCtx->addField('*');
        $qryCtx->addFilter(new StringFilter('TestUser','','id = :id'), self::$user->getId());

        $result = self::$testUserEntity->find($qryCtx);

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);
        $this->assertEquals(self::$loginDates[0], new \DateTime($result[0]['theLastLoginDate']));


        $qryCtx = new FindQueryContext('TestUser');
        $qryCtx->addField(new RelativeField(new Aggregate('MIN', 'credential.loginHistories.date')), 'theFirstLoginDate');
        $qryCtx->addField('*');
        $qryCtx->addFilter(new StringFilter('TestUser','','id = :id'), self::$user->getId());

        $result = self::$testUserEntity->find($qryCtx);

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);
        $this->assertEquals(end(self::$loginDates), new \DateTime($result[0]['theFirstLoginDate']));

    }

    public function testAggregateAsField() {

        $qryCtx = new FindQueryContext('TestUser');
        $qryCtx->addField('*');
        $qryCtx->addField('lastLoginDate');
        $qryCtx->addFilter(new StringFilter('TestUser','','id = :id'), self::$user->getId());

        $result = self::$testUserEntity->find($qryCtx);

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);
        $this->assertArrayHasKey(0, $result);
        $this->assertInstanceOf('\Core\Model\TestUser', $result[0]);
        /**
         * @var $user TestUser
         */
        $user = $result[0];

        $this->assertEquals(self::$loginDates[0], $user->getLastLoginDate());

    }

} 