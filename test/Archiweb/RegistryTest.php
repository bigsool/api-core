<?php


namespace Archiweb;


use Archiweb\Context\ApplicationContext;
use Archiweb\Context\FindQueryContext;
use Archiweb\Expression\BinaryExpression;
use Archiweb\Expression\KeyPath;
use Archiweb\Expression\Parameter;
use Archiweb\Expression\Value;
use Archiweb\Filter\ExpressionFilter;
use Archiweb\Model\Product;
use Archiweb\Operator\EqualOperator;

class RegistryTest extends TestCase {

    /**
     * @var array
     */
    protected static $doctrineConnectionSettings;

    /**
     * @var ApplicationContext
     */
    protected $appCtx;

    public function setUp () {

        parent::setUp();

        $this->appCtx = $this->getApplicationContext(self::$doctrineConnectionSettings);
        $this->appCtx->addField(new StarField('Product'));
        $this->appCtx->addField(new Field('Product', 'name'));
        $this->appCtx->addField(new Field('Product', 'price'));

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
        $product->setName('produit 1');
        $product->setBundleid('the product bundle id');
        $product->setConsumable(true);
        $product->setPrice(12.5);
        $product->setWeight(2);
        $product->setAvailable(true);
        $product->setVat(13.5);

        $registry = $this->appCtx->getNewRegistry();
        $registry->save($product);

        $this->assertEquals(1, $product->getId());

    }
    /*
        public function testSaveWithDependencies () {

            $company = new Company();
            $company->setName('company name');
            $user = new User();
            $user->setEmail('user@email.com');
            $company->setOwner($user);

            $storage = new Storage();

            $company->addUser($user);
            $company->setStorage($storage);

            $registry = $this->appCtx->getNewRegistry();
            $registry->save($company);

        }
    */
    /**
     * @expectedException \Exception
     */
    public function testSaveWrongClass () {

        $registry = $this->appCtx->getNewRegistry();
        $registry->save(new \stdClass());

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
        $this->assertSame(['id'         => 1,
                           'duration'   => NULL,
                           'bundleid'   => 'the product bundle id',
                           'name'       => 'produit 1',
                           'consumable' => true,
                           'price'      => 12.5,
                           'weight'     => 2,
                           'available'  => true,
                           'vat'        => 13.5
                          ], $result[0]);
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
        $this->assertSame(['name'  => 'produit 1',
                           'price' => 12.5
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
        $this->assertSame('the product bundle id', $result[0]->getBundleId());
        $this->assertSame('produit 1', $result[0]->getName());
        $this->assertSame(12.5, $result[0]->getPrice());
        $this->assertSame(2, $result[0]->getWeight());
        $this->assertSame(13.5, $result[0]->getVat());
        $this->assertTrue($result[0]->getAvailable());
        $this->assertTrue($result[0]->getConsumable());
        $this->assertSame('SELECT product FROM \Archiweb\Model\Product product', $registry->getLastExecutedQuery());
        // TODO: improve test

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
     * @expectedException \Exception
     */
    public function testFindWithoutFields () {

        $qryCtx = new FindQueryContext($this->appCtx, 'Product');
        $this->appCtx->getNewRegistry()->find($qryCtx);

    }

}