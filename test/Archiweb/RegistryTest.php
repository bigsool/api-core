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
    protected static $appCtx;

    public static function setUpBeforeClass() {

        parent::setUpBeforeClass();

        self::$appCtx = self::getApplicationContext();
        self::$appCtx->addField(new StarField('Produit'));

        self::resetDatabase(self::$appCtx);

    }

    public function testSave () {

        $product = new Product();
        $product->setName('produit 1');

        $registry = self::$appCtx->getNewRegistry();
        $registry->save($product);

    }

    public function testSaveWithDependencies () {

        $company = new Company();
        $company->setName('company name');

        $user = new User();
        $user->setEmail('user@email.com');

        $storage = new Storage();

        $company->addUser($user);
        $company->setStorage($storage);

        $registry = self::$appCtx->getNewRegistry();
        $registry->save($company);

    }

    /**
     * @expectedException \Exception
     */
    public function testSaveWrongClass () {

        $registry = self::$appCtx->getNewRegistry();
        $registry->save(new \stdClass());

    }

    /**
     * @depends testSave
     */
    public function testFindWithoutFilter () {

        $qryCtx = $this->getFindQueryContext('Produit');
        $qryCtx->addField(self::$appCtx->getFieldByEntityAndName('Produit', '*'));

        $registry = self::$appCtx->getNewRegistry();
        $result = $registry->find($qryCtx);

        $this->assertInternalType('array', $result);
        // TODO: improve test

    }

}