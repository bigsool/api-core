<?php


namespace Core;


use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Context\QueryContext;
use Core\Expression\BinaryExpression;
use Core\Expression\KeyPath as ExpressionKeyPath;
use Core\Expression\Parameter;
use Core\Expression\Value;
use Core\Field\Field;
use Core\Field\KeyPath as FieldKeyPath;
use Core\Field\StarField;
use Core\Filter\ExpressionFilter;
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
        $this->appCtx->addField(new StarField('TestCompany'));
        $this->appCtx->addField(new Field('TestCompany', 'name'));
        $this->appCtx->addField(new Field('TestCompany', 'zipCode'));

        $expression = new BinaryExpression(new EqualOperator(), new ExpressionKeyPath('confirmationKey'), new Value(1));
        $userConfKeyFilter = new ExpressionFilter('TestUser', 'confirmationKey', $expression);
        $this->appCtx->addFilter($userConfKeyFilter);

        $userStarField = new StarField('TestUser');
        $this->appCtx->addField($userStarField);
        $this->appCtx->addField(new Field('TestUser', 'name'));
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
        $company->setAddress(new UnsafeParameter($this->company['address']));

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
        $qryCtx->addKeyPath(new FieldKeyPath('*'));

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
        $qryCtx->addKeyPath(new FieldKeyPath('name'));
        $qryCtx->addKeyPath(new FieldKeyPath('tva'));

        $registry = $this->appCtx->getNewRegistry();
        $result = $registry->find($qryCtx);

        $this->assertInternalType('array', $result);
        $this->assertCount(2, $result);
        $this->assertSame(['name' => $this->company['name'],
                           'tva'  => $this->company['tva']
                          ], $result[0]);
        // TODO: improve test

    }

    /**
     * @depends testSaveWithRequiredParams
     */
    public function testFindWithoutFilterAsObject () {

        $qryCtx = new FindQueryContext('TestCompany');
        $qryCtx->addKeyPath(new FieldKeyPath('*'));

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
        $qryCtx->addKeyPath(new FieldKeyPath('*'));

        $expression = new BinaryExpression(new EqualOperator(), new ExpressionKeyPath('tva'), new Value(17));
        $qryCtx->addFilter(new ExpressionFilter('TestCompany', 'myStringFilter', $expression));
        $registry = $this->appCtx->getNewRegistry();
        $result = $registry->find($qryCtx, false);
        $dql = 'SELECT testCompany ' .
               'FROM \Core\Model\TestCompany testCompany ' .
               'WHERE ((testCompany.tva = 17))';
        $this->assertSame($dql, $registry->getLastExecutedQuery());

        $parameter = new Parameter(':tva');
        $expression = new BinaryExpression(new EqualOperator(), $parameter, new ExpressionKeyPath('tva'));
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

        $fieldKeyPath = new FieldKeyPath('name');
        $reqCtx = $this->getRequestContext();
        $reqCtx->setReturnedKeyPaths([$fieldKeyPath]);
        $reqCtx->setReturnedRootEntity('TestUser');
        $qryCtx = new FindQueryContext('TestUser', $reqCtx);
        $qryCtx->addKeyPath($fieldKeyPath);

        $registry = $this->appCtx->getNewRegistry();
        $registry->find($qryCtx, false);

        $dql = 'SELECT testUser.name ' .
               'FROM \Core\Model\TestUser testUser ' .
               'WHERE ((testUser.confirmationKey = 1))';
        $this->assertSame($dql, $registry->getLastExecutedQuery());

    }

    /**
     *
     */
    public function testFindWithAlias () {

        $this->appCtx->addField(new Field('TestUser', 'email'));
        $this->appCtx->addField(new Field('TestUser', 'name'));
        $this->appCtx->addField(new Field('TestCompany', 'name'));

        $qryCtx = new FindQueryContext('TestUser');
        $qryCtx->addKeyPath(new FieldKeyPath('email'));
        $qryCtx->addKeyPath(new FieldKeyPath('name'), 'userName');
        $qryCtx->addKeyPath(new FieldKeyPath('company.name'), 'companyName');

        $registry = $this->appCtx->getNewRegistry();
        $registry->find($qryCtx, false);

        $dql = 'SELECT testUser.email, testUser.name AS userName, testUserCompany.name AS companyName ' .
               'FROM \Core\Model\TestUser testUser ' .
               'INNER JOIN testUser.company testUserCompany';
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

        $qryCtx = new FindQueryContext('Qwe');
        $this->appCtx->getNewRegistry()->find($qryCtx);

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
        $account->setStorage($storage);

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

}