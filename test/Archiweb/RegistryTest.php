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

    public function testSaveWithoutRequiredParams () {

        $product = new Product();
        $product->setName('produit 1');

        $registry = self::$appCtx->getNewRegistry();
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

        $registry = self::$appCtx->getNewRegistry();
        $registry->save($product);

    }

    public function testSaveWithDependencies () {

        $company = new Company();
        $company->setName('company name');
        $user = new User();
        $user->setEmail('user@email.com');
        $company->setOwner($user);

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
        var_dump(json_encode($result));
        // TODO: improve test


    }

}