<?php


namespace Archiweb;


use Archiweb\Context\ApplicationContext;
use Archiweb\Model\Company;
use Archiweb\Model\Product;
use Archiweb\Model\Storage;
use Archiweb\Model\User;

class RegistryTest extends TestCase {

    /**
     * @var ApplicationContext
     */
    protected $appCtx;

    public function setUp () {

        parent::setUp();

        $this->appCtx = $this->getApplicationContext();
        $this->appCtx->addField(new StarField('Produit'));

    }

    public static function setUpBeforeClass () {

        parent::setUpBeforeClass();

        self::resetDatabase(self::getApplicationContext());

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

    }

    public function testSaveWithDependencies () {

        $registry = $this->appCtx->getNewRegistry();

        $company = new Company();
        $company->setName('company name');
        $user = new User();
        $user->setEmail('user@email.com');
        $user->setCompany($company);
        $user->setOwnedCompany($company);
        $company->setOwner($user);

        $storage = new Storage();

        $company->addUser($user);
        $company->setStorage($storage);

        $registry->save($company);

    }

    /**
     * @expectedException \Exception
     */
    public function testSaveWrongClass () {

        $registry = $this->appCtx->getNewRegistry();
        $registry->save(new \stdClass());

    }

    /**
     * @depends testSave
     */
    public function testFindWithoutFilterAsArray () {

        $qryCtx = $this->getFindQueryContext('Produit');
        $qryCtx->addField($this->appCtx->getFieldByEntityAndName('Produit', '*'));

        $registry = $this->appCtx->getNewRegistry();
        $result = $registry->find($qryCtx);

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);
        $this->assertSame([], $result[0]);
        // TODO: improve test

    }

    /**
     * @depends testSave
     */
    public function testFindWithoutFilterAsObject () {

        $qryCtx = $this->getFindQueryContext('Produit');
        $qryCtx->addField($this->appCtx->getFieldByEntityAndName('Produit', '*'));

        $registry = $this->appCtx->getNewRegistry();
        $result = $registry->find($qryCtx, false);

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);
        $this->assertSame([], $result[0]);
        // TODO: improve test


    }

}