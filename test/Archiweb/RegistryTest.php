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

        // TODO: empty database

    }

    public function testSave () {

        $product = new Product();
        $product->setName('produit 1');

        $registry = $this->appCtx->getNewRegistry();
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

        $registry = $this->appCtx->getNewRegistry();
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
    public function testFindWithoutFilter () {

        $qryCtx = $this->getFindQueryContext('Produit');
        $qryCtx->addField($this->appCtx->getFieldByEntityAndName('Produit', '*'));

        $registry = $this->appCtx->getNewRegistry();
        $result = $registry->find($qryCtx);

        $this->assertInternalType('array', $result);
        // TODO: improve test

    }

}