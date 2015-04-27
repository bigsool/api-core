<?php


namespace Core;


use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Context\QueryContext;
use Core\Context\RequestContext;
use Core\Expression\BinaryExpression;
use Core\Expression\KeyPath;
use Core\Expression\Parameter;
use Core\Expression\Value;
use Core\Field\Aggregate;
use Core\Field\RelativeField;
use Core\Field\StarField;
use Core\Filter\ExpressionFilter;
use Core\Filter\StringFilter;
use Core\Model\TestAccount;
use Core\Model\TestCompany;
use Core\Model\TestStorage;
use Core\Model\TestUser;
use Core\Operator\EqualOperator;
use Core\Parameter\UnsafeParameter;
use Core\Rule\CallbackRule;
use Core\Rule\FieldRule;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;

class RegistryTest extends TestCase {

    /**
     * @var Connection
     */
    protected static $doctrineConnectionSettings;

    /**
     * @var ApplicationContext
     */
    protected $appCtx;

    /**
     * @var array
     */
    private $company = [
        'id'      => 1,
        'name'    => 'the company 1',
        'address' => 'My Address !!',
        'zipCode' => '75845',
        'city'    => 'Qaris',
        'state'   => 'SomeWhere',
        'country' => 'SomeCountry',
        'tel'     => '0125478++',
        'fax'     => NULL,
        'tva'     => 'SC785412547'
    ];

    public function setUp () {

        parent::setUp();

        self::resetApplicationContext();

        $this->appCtx = $this->getApplicationContext(self::$doctrineConnectionSettings);

        $expression = new BinaryExpression(new EqualOperator(), new KeyPath('confirmationKey'), new Value(1));
        $userConfKeyFilter = new ExpressionFilter('TestUser', 'confirmationKey', $expression);
        $this->appCtx->addFilter($userConfKeyFilter);

        $userStarField = new StarField('TestUser');
        $this->appCtx->addRule(new FieldRule($userStarField, $userConfKeyFilter));

    }

    public static function setUpBeforeClass () {

        parent::setUpBeforeClass();

        $ctx = self::getApplicationContext();


        $prop = new \ReflectionProperty($ctx, 'entityManager');
        $prop->setAccessible(true);

        $em = $prop->getValue($ctx);

        self::$doctrineConnectionSettings = $em->getConnection();
        self::resetDatabase($ctx);

    }

    public function tearDown () {

        parent::tearDown();

        $prop = new \ReflectionProperty($this->appCtx, 'entityManager');
        $prop->setAccessible(true);

        $em = $prop->getValue($this->appCtx);

    }

    /**
     * @expectedException \Exception
     */
    public function testSaveWithoutRequiredParams () {

        $company = new TestCompany();
        $company->setAddress('company address 1');

        $registry = $this->appCtx->getNewRegistry();
        $registry->save($company);

    }

    public function testSaveWithRequiredParams () {

        $company = new TestCompany();
        $company->setName($this->company['name']);
        $company->setAddress($this->company['address']);
        $company->setCity($this->company['city']);
        $company->setCountry($this->company['country']);
        $company->setFax($this->company['fax']);
        $company->setTel($this->company['tel']);
        $company->setTva($this->company['tva']);
        $company->setZipCode($this->company['zipCode']);
        $company->setState($this->company['state']);

        $registry = $this->appCtx->getNewRegistry();
        $registry->save($company);

        $this->assertEquals(1, $company->getId());

    }

    /**
     * @expectedException \Exception
     */
    public function testSaveWithUnsafeParameter () {

        $company = new TestCompany();
        $company->setName($this->company['name']);
        $company->setAddress(new UnsafeParameter($this->company['address'], ''));

        $registry = $this->appCtx->getNewRegistry();
        $registry->save($company);

        $this->assertEquals(1, $company->getId());

    }

    public function testSaveWithDependencies () {

        $company = new TestCompany();
        $company->setName('company name');
        $user = new TestUser();
        $user->setEmail('user@email.com');
        $user->setPassword('qwe');
        $user->setRegisterDate(new \DateTime());
        $company->setOwner($user);
        $user->setOwnedCompany($company);

        $storage = new TestStorage();
        $storage->setUrl('url');
        $storage->setLogin('login');
        $storage->setPassword('qwe');
        $storage->setUsedspace(0);
        $storage->setLastusedspaceupdate(new \DateTime());
        $storage->setIsoutofquota(false);
        $storage->setCompany($company);

        $company->addUser($user);
        $company->setStorage($storage);
        $user->setCompany($company);

        $registry = $this->appCtx->getNewRegistry();

        $registry->save($company);

        $em = $this->getEntityManager(self::$doctrineConnectionSettings);
        $result =
            $em->createQuery('SELECT c, s, o FROM \Core\Model\TestCompany c INNER JOIN c.storage s INNER JOIN c.owner o')
               ->getArrayResult();

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);
        $excepted =
            ['id'      => 2,
             'name'    => 'company name',
             'storage' => ['id' => 1, 'url' => 'url', 'login' => 'login', 'password' => 'qwe'],
             'owner'   => ['id' => 1, 'email' => 'user@email.com', 'password' => 'qwe']
            ];
        foreach ($excepted as $key => $value) {
            $this->assertArrayHasKey($key, $result[0]);
            if (!is_array($value)) {
                $this->assertSame($value, $result[0][$key]);
            }
            else {
                foreach ($value as $_key => $_value) {
                    $this->assertArrayHasKey($_key, $result[0][$key]);
                    $this->assertSame($_value, $result[0][$key][$_key]);
                }
            }
        }
    }

    /**
     * @param Connection $conn
     *
     * @return EntityManager
     */
    public function getEntityManager (Connection $conn) {

        $ctx = $this->getApplicationContext($conn);
        $prop = new \ReflectionProperty($ctx, 'entityManager');
        $prop->setAccessible(true);

        return $prop->getValue($ctx);

    }

    /**
     * @expectedException \Exception
     */
    public function testSaveWrongClass () {

        $registry = $this->appCtx->getNewRegistry();
        $registry->save(new \stdClass());

    }

    public function testSaveWithRule () {

        $callbackRule = new CallbackRule('blabla', function (QueryContext $ctx) {

            if ($ctx->getEntity() == "TestCompany") {
                return true;
            }

            return false;
        }, function (QueryContext $ctx) {

            throw new \RuntimeException('forbidden save !', 2014);
        }, array());

        $this->appCtx->addRule($callbackRule);

        $company = new TestCompany();
        $company->setName('the new company');

        $registry = $this->appCtx->getNewRegistry();
        $exceptionThrow = false;
        try {
            $registry->save($company);
        }
        catch (\RuntimeException $e) {
            $this->assertEquals($e->getCode(), 2014);
            $exceptionThrow = true;
        }
        $this->assertTrue($exceptionThrow);
        $em = $this->getEntityManager(self::$doctrineConnectionSettings);
        $result =
            $em->createQuery('SELECT c FROM \Core\Model\TestCompany c WHERE c.name = \'the new company\'')
               ->getArrayResult();
        $this->assertCount(0, $result);

    }

    /**
     * @depends testSaveWithRequiredParams
     */
    public function testFindWithoutFilterAsArray () {

        $qryCtx = new FindQueryContext('TestCompany');
        $qryCtx->addField(new RelativeField('*'));

        $registry = $this->appCtx->getNewRegistry();
        $result = $registry->find($qryCtx);

        $this->assertInternalType('array', $result);
        $this->assertCount(2, $result);
        $this->assertSame($this->company, $result[0]);
        // TODO: improve test

    }

    /**
     * @depends testSaveWithRequiredParams
     */
    public function testFindFieldsWithoutFilter () {

        $qryCtx = new FindQueryContext('TestCompany');
        $qryCtx->addField(new RelativeField('name'));
        $qryCtx->addField(new RelativeField('tva'));

        $registry = $this->appCtx->getNewRegistry();
        $result = $registry->find($qryCtx);

        $this->assertInternalType('array', $result);
        $this->assertCount(2, $result);
        $this->assertSame([
                              'name' => $this->company['name'],
                              'tva'  => $this->company['tva']
                          ], $result[0]);
        // TODO: improve test

    }

    /**
     * @depends testSaveWithRequiredParams
     */
    public function testFindWithoutFilterAsObject () {

        $qryCtx = new FindQueryContext('TestCompany');
        $qryCtx->addField(new RelativeField('*'));

        $registry = $this->appCtx->getNewRegistry();
        $result = $registry->find($qryCtx, false);

        $this->assertInternalType('array', $result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf('\Core\Model\TestCompany', $result[0]);

        $this->assertSame($this->company['address'], $result[0]->getAddress());
        $this->assertSame($this->company['name'], $result[0]->getName());
        $this->assertSame($this->company['zipCode'], $result[0]->getZipCode());
        $this->assertSame($this->company['tel'], $result[0]->getTel());
        $this->assertSame($this->company['tva'], $result[0]->getTva());
        $this->assertSame('SELECT testCompany FROM \Core\Model\TestCompany testCompany',
                          $registry->getLastExecutedQuery());

    }

    /**
     * @depends testSaveWithRequiredParams
     */
    public function testFindWithFilters () {

        $qryCtx = new FindQueryContext('TestCompany');
        $qryCtx->addField(new RelativeField('*'));

        $expression = new BinaryExpression(new EqualOperator(), new KeyPath('tva'), new Value(17));
        $qryCtx->addFilter(new ExpressionFilter('TestCompany', 'myStringFilter', $expression));
        $registry = $this->appCtx->getNewRegistry();
        $result = $registry->find($qryCtx, false);
        $dql = 'SELECT testCompany ' .
               'FROM \Core\Model\TestCompany testCompany ' .
               'WHERE ((testCompany.tva = 17))';
        $this->assertSame($dql, $registry->getLastExecutedQuery());

        $parameter = new Parameter(':tva');
        $expression = new BinaryExpression(new EqualOperator(), $parameter, new KeyPath('tva'));
        $qryCtx->setParams(['tva' => 126]);
        $qryCtx->addFilter(new ExpressionFilter('TestCompany', 'myStringFilter', $expression));
        $registry = $this->appCtx->getNewRegistry();
        $result = $registry->find($qryCtx, false);
        $dql = 'SELECT testCompany ' .
               'FROM \Core\Model\TestCompany testCompany ' .
               'WHERE ((testCompany.tva = 17) ' .
               'AND (' . $parameter->getRealName() . ' = testCompany.tva))';
        $this->assertSame($dql, $registry->getLastExecutedQuery());

    }

    /**
     *
     */
    public function testFindRuleOnFields () {

        $RelativeField = new RelativeField('name');
        $reqCtx = $this->getRequestContext();
        $reqCtx->setReturnedFields([$RelativeField]);
        $qryCtx = new FindQueryContext('TestUser', $reqCtx);
        $qryCtx->addField($RelativeField);

        $registry = $this->appCtx->getNewRegistry();
        $registry->find($qryCtx, false);

        $dql = 'SELECT testUser ' .
               'FROM \Core\Model\TestUser testUser ' .
               'WHERE ((testUser.confirmationKey = 1))';
        $this->assertSame($dql, $registry->getLastExecutedQuery());

    }

    /**
     *
     */
    public function testFindWithAlias () {

        $qryCtx = new FindQueryContext('TestUser');
        $qryCtx->addField(new RelativeField(new Aggregate('count', ['*'])), 'nbUsers');
        $qryCtx->addField(new RelativeField('email'));
        $qryCtx->addField(new RelativeField('name'), 'userName');
        $qryCtx->addField(new RelativeField('company.name'), 'companyName');


        $registry = $this->appCtx->getNewRegistry();
        $registry->find($qryCtx, true);

        $dql =
            'SELECT count(testUser) AS nbUsers, testUser, testUserCompany ' .
            'FROM \Core\Model\TestUser testUser ' .
            'INNER JOIN testUser.company testUserCompany ' .
            'GROUP BY testUser,testUserCompany';
        $this->assertSame($dql, $registry->getLastExecutedQuery());

    }

    /**
     * @expectedException \Exception
     */
    public function testFindWithoutFields () {

        $qryCtx = new FindQueryContext('TestCompany');
        $this->appCtx->getNewRegistry()->find($qryCtx);

    }

    /**
     * @expectedException \Exception
     */
    public function testFindWithBadEntity () {

        $qryCtx = $this->getMockFindQueryContext();
        $reqCtx = $this->getMockRequestContext();
        $reqCtx->method('getReturnedRootEntity')->willReturn('Qweee');
        $qryCtx->method('getReqCtx')->willReturn($reqCtx);
        $qryCtx->method('getEntity')->willReturn('Qwe');
        $this->appCtx->getNewRegistry()->find($qryCtx);

    }

    public function testFindWithRequestedKeyPath () {

        $reqCtx = new RequestContext();
        $reqCtx->setFormattedReturnedFields([
                                                new RelativeField('name')
                                            ]);

        $qryCtx = new FindQueryContext('TestUser', $reqCtx);

        $qryCtx->addField(new RelativeField('email'));

        $registry = $this->appCtx->getNewRegistry();
        $registry->find($qryCtx, true);

        $dql = 'SELECT testUser ' .
               'FROM \Core\Model\TestUser testUser ' .
               'WHERE ((testUser.confirmationKey = 1))';

        $this->assertSame($dql, $registry->getLastExecutedQuery());

    }

    public function testFindWithTwoIdenticalEntities () {

        $reqCtx = new RequestContext();
        //$reqCtx->setReturnedKeyPaths([new RelativeField('email'),new RelativeField('company.users.email')]);

        $qryCtx = new FindQueryContext('TestUser', $reqCtx);

        $qryCtx->addField(new RelativeField('name'));
        $qryCtx->addField(new RelativeField('company.users.name'));
        $qryCtx->addField(new RelativeField('company.users.email'));

        $registry = $this->appCtx->getNewRegistry();
        $registry->find($qryCtx, false);

        $dql = 'SELECT testUser, testUserCompany, testUserCompanyUsers ' .
               'FROM \Core\Model\TestUser testUser ' .
               'INNER JOIN testUser.company testUserCompany ' .
               'INNER JOIN testUserCompany.users testUserCompanyUsers';

        $this->assertSame($dql, $registry->getLastExecutedQuery());

    }

    public function testSaveMagicalEntity () {

        $company = new TestCompany();
        $company->setName('company name2');
        $user = new TestUser();
        $user->setEmail('user2@email.com');
        $user->setPassword('qwe');
        $user->setRegisterDate(new \DateTime());
        $company->setOwner($user);
        $user->setOwnedCompany($company);

        $storage = new TestStorage();
        $storage->setUrl('url2');
        $storage->setLogin('login2');
        $storage->setPassword('qwe2');
        $storage->setUsedspace(0);
        $storage->setLastusedspaceupdate(new \DateTime());
        $storage->setIsoutofquota(false);

        $account = new TestAccount($user);
        $account->setCompany($company);
        $account->setCompanyStorage($storage);

        $registry = $this->appCtx->getNewRegistry();

        $registry->save($account);

        $em = $this->getEntityManager(self::$doctrineConnectionSettings);
        $result =
            $em->createQuery('SELECT c, s, o FROM \Core\Model\TestCompany c INNER JOIN c.storage s INNER JOIN c.owner o WHERE c.id = '
                             . $company->getId())
               ->getArrayResult();

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);
        $excepted =
            ['id'      => $company->getId(),
             'name'    => 'company name2',
             'storage' => ['id' => $storage->getId(), 'url' => 'url2', 'login' => 'login2', 'password' => 'qwe2'],
             'owner'   => ['id' => $user->getId(), 'email' => 'user2@email.com', 'password' => 'qwe']
            ];
        foreach ($excepted as $key => $value) {
            $this->assertArrayHasKey($key, $result[0]);
            if (!is_array($value)) {
                $this->assertSame($value, $result[0][$key]);
            }
            else {
                foreach ($value as $_key => $_value) {
                    $this->assertArrayHasKey($_key, $result[0][$key]);
                    $this->assertSame($_value, $result[0][$key][$_key]);
                }
            }
        }

    }

    public function testRestrictedEntities () {

        $owner = new TestUser();
        $owner->setEmail('owner@company.com');
        $owner->setPassword('qwe');
        $owner->setRegisterDate(new \DateTime());

        $company = new TestCompany();
        $company->setName('owned company');

        $company->setOwner($owner);
        $owner->setOwnedCompany($company);
        $company->addUser($owner);
        $owner->setCompany($company);

        foreach (range(1, 5) as $subUserNb) {
            $subUser = new TestUser();
            $subUser->setEmail("subUser{$subUserNb}@company.com");
            $subUser->setPassword('qwe');
            $subUser->setRegisterDate(new \DateTime());
            $company->addUser($subUser);
            $subUser->setCompany($company);
        }

        $saveRegistry = $this->appCtx->getNewRegistry();
        $saveRegistry->save($owner);

        $reqCtx = new RequestContext();
        $qryCtx = new FindQueryContext('TestUser', $reqCtx);
        $filter = new StringFilter('TestUser', 'companyOwnerOnly', 'ownedCompany.id = ' . $company->getId());
        $qryCtx->addFilter($filter);
        $filter->setAliasForEntityToUse('testUser');

        $qryCtx->addField(new RelativeField('*'));
        $qryCtx->addField(new RelativeField('company.users.*'));

        $findRegistry = $this->appCtx->getNewRegistry();
        $result = $findRegistry->find($qryCtx, false);

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);

        /**
         * @var TestUser $user
         */
        $user = $result[0];
        $this->assertInstanceOf('\Core\Model\TestUser', $user);

        $this->assertNull($user->getOwnedCompany());
        $this->assertInstanceOf('\Core\Model\TestCompany', $user->getUnrestrictedOwnedCompany());
        $this->assertInstanceOf('\Core\Model\TestCompany', $user->getCompany());

        /**
         * @var TestUser[] $users
         */
        $users = $user->getCompany()->getUsers();
        $this->assertCount(6, $users);
        $this->assertContainsOnlyInstancesOf('\Core\Model\TestUser', $users);

        $this->assertSame($user, $users[0]);
        $this->assertNull($users[1]->getCompany());
        $this->assertNull($users[1]->getUnrestrictedOwnedCompany());
        $this->assertInstanceOf('\Core\Model\TestCompany', $users[1]->getUnrestrictedCompany());

    }

}