<?php


namespace Archiweb;


use Archiweb\Context\ApplicationContext;
use Archiweb\Context\FindQueryContext;
use Archiweb\Context\QueryContext;
use Archiweb\Expression\BinaryExpression;
use Archiweb\Expression\KeyPath;
use Archiweb\Expression\Parameter;
use Archiweb\Expression\Value;
use Archiweb\Field\Field;
use Archiweb\Field\StarField;
use Archiweb\Filter\ExpressionFilter;
use Archiweb\Model\Company;
use Archiweb\Model\Product;
use Archiweb\Model\Storage;
use Archiweb\Model\User;
use Archiweb\Operator\EqualOperator;
use Archiweb\Rule\CallbackRule;
use Archiweb\Rule\FieldRule;
use Archiweb\Rule\SimpleRule;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use SebastianBergmann\Comparator\ExceptionComparatorTest;
use SebastianBergmann\Exporter\Exception;

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
    private $product = ['id'         => 1,
                        'duration'   => NULL,
                        'bundleid'   => 'the product bundle id',
                        'name'       => 'produit 1',
                        'consumable' => true,
                        'price'      => 12.5,
                        'weight'     => 2,
                        'available'  => true,
                        'vat'        => 13.5
    ];

    public function setUp () {

        parent::setUp();

        $this->appCtx = $this->getApplicationContext(self::$doctrineConnectionSettings);
        $this->appCtx->addField(new StarField('Product'));
        $this->appCtx->addField(new Field('Product', 'name'));
        $this->appCtx->addField(new Field('Product', 'price'));

        $expression = new BinaryExpression(new EqualOperator(), new KeyPath('consumable'), new Value(1));
        $funcConsumableFilter = new ExpressionFilter('Functionality', 'consumable', 'SELECT', $expression);
        $this->appCtx->addFilter($funcConsumableFilter);

        $funcConsumableRule = new SimpleRule('funcConsumableRule', function () {

            return false;

        }, $funcConsumableFilter);

        $funcStarField = new StarField('Functionality');
        $this->appCtx->addField($funcStarField);
        $this->appCtx->addRule(new FieldRule($funcStarField, $funcConsumableRule));

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

        parent::setUp();

        $prop = new \ReflectionProperty($this->appCtx, 'entityManager');
        $prop->setAccessible(true);

        $em = $prop->getValue($this->appCtx);
        $queries = $em->getConfiguration()->getSQLLogger()->queries;

        if ($queries) {
            var_dump($em->getConfiguration()->getSQLLogger()->queries);
        }

    }

    /**
     * @expectedException \Exception
     */
    public function testSaveWithoutRequiredParams () {

        $product = new Product();
        $product->setName('produit 1');

        $registry = $this->appCtx->getNewRegistry();
        $registry->save($product);

    }

    public function testSaveWithRequiredParams () {

        $product = new Product();
        $product->setName($this->product['name']);
        $product->setBundleid($this->product['bundleid']);
        $product->setConsumable($this->product['consumable']);
        $product->setPrice($this->product['price']);
        $product->setWeight($this->product['weight']);
        $product->setAvailable($this->product['available']);
        $product->setVat($this->product['vat']);

        $registry = $this->appCtx->getNewRegistry();
        $registry->save($product);

        $this->assertEquals(1, $product->getId());

    }

    public function testSaveWithDependencies () {

        $company = new Company();
        $company->setName('company name');
        $user = new User();
        $user->setEmail('user@email.com');
        $user->setPassword('qwe');
        $user->setRegisterDate(new \DateTime());
        $company->setOwner($user);
        $user->setOwnedCompany($company);

        $storage = new Storage();
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
            $em->createQuery('SELECT c, s, o FROM \Archiweb\Model\Company c INNER JOIN c.storage s INNER JOIN c.owner o')
               ->getArrayResult();

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);
        $excepted =
            ['id'      => 1,
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

        $callbackRule = new CallbackRule('blabla',function(QueryContext $ctx) {
            if ($ctx->getEntity() == "Product") return true;
            return false;
        },function(QueryContext $ctx){throw new \RuntimeException('forbidden save !',2014);},array());

        $this->appCtx->addRule($callbackRule);

        $product = new Product();
        $product->setName('the new product');
        $product->setBundleid('the new product bundle id');
        $product->setConsumable($this->product['consumable']);
        $product->setPrice($this->product['price']);
        $product->setWeight($this->product['weight']);
        $product->setAvailable($this->product['available']);
        $product->setVat($this->product['vat']);

        $registry = $this->appCtx->getNewRegistry();
        $exceptionThrow = false;
        try {
            $registry->save($product);
        }
        catch (\RuntimeException $e) {
            $this->assertEquals($e->getCode(),2014);
            $exceptionThrow = true;
        }
        $this->assertTrue($exceptionThrow);
        $em = $this->getEntityManager(self::$doctrineConnectionSettings);
        $result = $em->createQuery('SELECT p FROM \Archiweb\Model\Product p WHERE p.name = \'the new product\'')->getArrayResult();
        $this->assertCount(0, $result);

    }

    /**
     * @depends testSaveWithRequiredParams
     */
    public function testFindWithoutFilterAsArray () {

        $qryCtx = new FindQueryContext($this->appCtx, 'Product');
        $qryCtx->addKeyPath(new KeyPath('*'));

        $registry = $this->appCtx->getNewRegistry();
        $result = $registry->find($qryCtx);

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);
        $this->assertSame($this->product, $result[0]);
        // TODO: improve test

    }

    /**
     * @depends testSaveWithRequiredParams
     */
    public function testFindFieldsWithoutFilter () {

        $qryCtx = new FindQueryContext($this->appCtx, 'Product');
        $qryCtx->addKeyPath(new KeyPath('name'));
        $qryCtx->addKeyPath(new KeyPath('price'));

        $registry = $this->appCtx->getNewRegistry();
        $result = $registry->find($qryCtx);

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);
        $this->assertSame(['name'  => $this->product['name'],
                           'price' => $this->product['price']
                          ], $result[0]);
        // TODO: improve test

    }

    /**
     * @depends testSaveWithRequiredParams
     */
    public function testFindWithoutFilterAsObject () {

        $qryCtx = new FindQueryContext($this->appCtx, 'Product');
        $qryCtx->addKeyPath(new KeyPath('*'));

        $registry = $this->appCtx->getNewRegistry();
        $result = $registry->find($qryCtx, false);

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf('\Archiweb\Model\Product', $result[0]);
        $this->assertSame($this->product['bundleid'], $result[0]->getBundleId());
        $this->assertSame($this->product['name'], $result[0]->getName());
        $this->assertSame($this->product['price'], $result[0]->getPrice());
        $this->assertSame($this->product['weight'], $result[0]->getWeight());
        $this->assertSame($this->product['vat'], $result[0]->getVat());
        $this->assertTrue($result[0]->getAvailable());
        $this->assertTrue($result[0]->getConsumable());
        $this->assertSame('SELECT product FROM \Archiweb\Model\Product product', $registry->getLastExecutedQuery());

    }

    /**
     * @depends testSaveWithRequiredParams
     */
    public function testFindWithFilters () {

        $qryCtx = new FindQueryContext($this->appCtx, 'Product');
        $qryCtx->addKeyPath(new KeyPath('*'));

        $expression = new BinaryExpression(new EqualOperator(), new KeyPath('price'), new Value(17));
        $qryCtx->addFilter(new ExpressionFilter('Product', 'myStringFilter', 'SELECT', $expression));
        $registry = $this->appCtx->getNewRegistry();
        $result = $registry->find($qryCtx, false);
        $dql = 'SELECT product ' .
               'FROM \Archiweb\Model\Product product ' .
               'WHERE product.price = 17';
        $this->assertSame($dql, $registry->getLastExecutedQuery());

        $parameter = new Parameter(':price');
        $expression = new BinaryExpression(new EqualOperator(), $parameter, new KeyPath('price'));
        $qryCtx->setParams(['price' => 126]);
        $qryCtx->addFilter(new ExpressionFilter('Product', 'myStringFilter', 'SELECT', $expression));
        $registry = $this->appCtx->getNewRegistry();
        $result = $registry->find($qryCtx, false);
        $dql = 'SELECT product ' .
               'FROM \Archiweb\Model\Product product ' .
               'WHERE product.price = 17 ' .
               'AND ' . $parameter->getRealName() . ' = product.price';
        $this->assertSame($dql, $registry->getLastExecutedQuery());

    }

    /**
     *
     */
    public function testFindRuleOnFields () {

        $qryCtx = new FindQueryContext($this->appCtx, 'Functionality');
        $qryCtx->addKeyPath(new KeyPath('*'));

        $registry = $this->appCtx->getNewRegistry();
        $result = $registry->find($qryCtx, false);

        $dql = 'SELECT functionality ' .
               'FROM \Archiweb\Model\Functionality functionality ' .
               'WHERE functionality.consumable = 1';
        $this->assertSame($dql, $registry->getLastExecutedQuery());

    }

    /**
     * @expectedException \Exception
     */
    public function testFindWithoutFields () {

        $qryCtx = new FindQueryContext($this->appCtx, 'Product');
        $this->appCtx->getNewRegistry()->find($qryCtx);

    }

    /**
     * @expectedException \Exception
     */
    public function testFindWithBadEntity () {

        $qryCtx = new FindQueryContext($this->appCtx, 'Qwe');
        $this->appCtx->getNewRegistry()->find($qryCtx);

    }

}