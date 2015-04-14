<?php


namespace Core\Field;

use Core\Context\FindQueryContext;
use Core\Model\TestCompany;
use Core\Model\TestStorage;
use Core\Model\TestUser;
use Core\TestCase;

class AggregateTest extends TestCase {

    /**
     * @var \Core\Model\TestCompany
     */
    protected static $company1;

    /**
     * @var \Core\Model\TestCompany
     */
    protected static $company2;

    /**
     * @var \Core\Model\TestUser
     */
    protected static $user1;

    /**
     * @var \Core\Model\TestUser
     */
    protected static $user2;

    /**
     * @var \Core\Model\TestUser
     */
    protected static $user3;

    /**
     * @var \Core\Model\TestStorage
     */
    protected static $storage;

    /**
     * @var \Core\Model\TestStorage
     */
    protected static $storage2;

    public static function setUpBeforeClass () {

        parent::setUpBeforeClass();

        self::resetDatabase(self::getApplicationContext());

        self::$company1 = new TestCompany();
        self::$company1->setName('Bigsool');
        self::$company2 = new TestCompany();
        self::$company2->setName('CGI');

        self::$user1 = new TestUser();
        self::$user1->setEmail('thierry@bigsool.com');
        self::$user1->setPassword('qwe');
        self::$user1->setCompany(self::$company1);
        self::$user1->setRegisterDate(new \DateTime());
        self::$user2 = new TestUser();
        self::$user2->setEmail('julien@bigsool.com');
        self::$user2->setCompany(self::$company1);
        self::$user2->setPassword('qwe');
        self::$user2->setRegisterDate(new \DateTime());
        self::$user3 = new TestUser();
        self::$user3->setEmail('thomas@cgi.com');
        self::$user3->setCompany(self::$company2);
        self::$user3->setPassword('qwe');
        self::$user3->setRegisterDate(new \DateTime());
        self::$company1->setOwner(self::$user2);


        self::$company2->setOwner(self::$user3);

        self::$storage = new TestStorage();
        self::$storage->setUrl('http://www.amazon.com/');
        self::$storage->setCompany(self::$company1);
        self::$storage->setLogin('login');
        self::$storage->setPassword('qwe');
        self::$storage->setUsedspace(0);
        self::$storage->setLastusedspaceupdate(new \DateTime());
        self::$storage->setIsoutofquota(false);
        self::$company1->setStorage(self::$storage);
        self::$company1->addUser(self::$user1);
        self::$company1->addUser(self::$user2);

        self::$storage2 = new TestStorage();
        self::$storage2->setUrl('http://www.amazon.com/');
        self::$storage2->setCompany(self::$company1);
        self::$storage2->setLogin('login');
        self::$storage2->setPassword('qwe');
        self::$storage2->setUsedspace(0);
        self::$storage2->setLastusedspaceupdate(new \DateTime());
        self::$storage2->setIsoutofquota(false);
        self::$company2->addUser(self::$user3);
        self::$company2->setStorage(self::$storage2);

        $registry = self::getApplicationContext()->getNewRegistry();
        $registry->save(self::$user1);
        $registry->save(self::$user2);
        $registry->save(self::$user3);
        $registry->save(self::$company1);
        $registry->save(self::$company2);
        $registry->save(self::$storage);
        $registry->save(self::$storage2);

        self::$user1->setRegisterDate(new \DateTime());
        self::$user2->setRegisterDate(new \DateTime());
        self::$user3->setRegisterDate(new \DateTime());

        self::$storage->setLastUsedSpaceUpdate(new \DateTime());

        self::$storage2->setLastUsedSpaceUpdate(new \DateTime());

    }

    /**
     *
     */
    public function testResolve () {

        $registry = self::getApplicationContext()->getNewRegistry();

        $qryCtx = new FindQueryContext('TestUser');
        $qryCtx->addField(new Aggregate('count', ['*']), 'nbUsers');

        $result = $registry->find($qryCtx);

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);
        $this->assertSame("3", $result[0]['nbUsers']);

        $registry = self::getApplicationContext()->getNewRegistry();

        $qryCtx = new FindQueryContext('TestUser');
        $qryCtx->addField(new Aggregate('max', ['company.id']), 'maxCompanyId');

        $result = $registry->find($qryCtx);

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);
        $this->assertSame("2", $result[0]['maxCompanyId']);

        $registry = self::getApplicationContext()->getNewRegistry();

        $qryCtx = new FindQueryContext('TestUser');
        $qryCtx->addField(new Aggregate('min', ['company.id']), 'minCompanyId');
        $qryCtx->addField(new RelativeField('*'));

        $result = $registry->find($qryCtx, false);

        $this->assertInternalType('array', $result);
        $this->assertCount(3, $result);
        $this->assertSame("1", $result[0]['minCompanyId']);

    }

} 