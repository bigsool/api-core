<?php

namespace Core;

use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Context\RequestContext;
use Core\Field\Aggregate;
use Core\Field\KeyPath;
use Core\Filter\StringFilter;
use Core\Model\TestAccount;
use Core\Model\TestCompany;
use Core\Model\TestStorage;
use Core\Model\TestUser;

class SerializerTest extends TestCase {


    /**
     * @var array
     */
    protected static $expected;

    /**
     * @var \Core\Model\TestCompany
     */
    protected static $company1;

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


    public static function setUpBeforeClass () {

        parent::setUpBeforeClass();

        self::resetDatabase(self::getApplicationContext());

        self::$company1 = new TestCompany();
        self::$company1->setName('Bigsool');
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
        self::$user3->setEmail('thomas@bigsool.com');
        self::$user3->setCompany(self::$company1);
        self::$user3->setPassword('qwe');
        self::$user3->setRegisterDate(new \DateTime());
        self::$company1->setOwner(self::$user3);

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
        self::$company1->addUser(self::$user3);

        self::$account1 = new TestAccount(self::$user1);
        self::$account2 = new TestAccount(self::$user2);

        $registry = self::getApplicationContext()->getNewRegistry();
        $registry->save(self::$user1);
        $registry->save(self::$user2);
        $registry->save(self::$user3);
        $registry->save(self::$company1);
        $registry->save(self::$storage);


        self::$user1->setRegisterDate((new \DateTime())->getTimestamp());
        self::$user2->setRegisterDate((new \DateTime())->getTimestamp());
        self::$user3->setRegisterDate((new \DateTime())->getTimestamp());

        self::$storage->setLastUsedSpaceUpdate((new \DateTime())->getTimestamp());


        self::$expected = [
            [
                'nbUsers' => '1',
                'email'   => self::$user2->getEmail(),
                'company' => [
                    'name' => self::$company1->getName(),
                    'storage' => [
                        'url' => self::$storage->getUrl(),
                    ]
                ]
            ],
            [
                'nbUsers' => '1',
                'email'   => self::$user1->getEmail(),
                'company' => [
                    'name' => self::$company1->getName(),
                    'storage' => [
                        'url' => self::$storage->getUrl(),
                    ]
                ]
            ],
            [
                'nbUsers' => '1',
                'email'   => self::$user3->getEmail(),
                'company' => [
                    'name' => self::$company1->getName(),
                    'storage' => [
                        'url' => self::$storage->getUrl(),
                    ]
                ]
            ],
        ];

    }

    private function findUsers ($reqCtx) {

        $qryCtx = new FindQueryContext('TestUser', $reqCtx);
        $qryCtx->addKeyPath(new Aggregate('count',['*']),'nbUsers');
        $qryCtx->addKeyPath(new KeyPath('email'),'userEmail');
        $qryCtx->addKeyPath(new KeyPath('company.name'),'companyName');
        $qryCtx->addKeyPath(new KeyPath('company.storage.url'), 'storageUrl');

        return ApplicationContext::getInstance()->getNewRegistry()->find($qryCtx);
    }

    public function testSerialize () {

        $reqCtx = new RequestContext();

        $reqCtx->setReturnedRootEntity('TestUser');

        $users = $this->findUsers($reqCtx);

        $emailKP =  new KeyPath('email');
        $nbUsersKP = new KeyPath('nbUsers');
        $companyIdKP = new KeyPath('company.name');
        $storageUrlKP = new KeyPath('company.storage.url');
        $emailKP->setAlias('userEmail');
        $nbUsersKP->setAlias('nbUsers');
        $companyIdKP->setAlias('companyName');
        $storageUrlKP->setAlias('storageUrl');

        $reqCtx->setReturnedKeyPaths([$nbUsersKP,$emailKP,$companyIdKP,$storageUrlKP]);

        $serializer = new Serializer($reqCtx);

        $serializer->serialize($users);
        $this->assertSame(self::$expected, $serializer->get());

    }

    public function testArrayWithoutKeyPath () {

        $reqCtx = new RequestContext();
        $reqCtx->setReturnedRootEntity('TestUser');
        $reqCtx->setReturnedRootEntity(NULL);

        $serializer = new Serializer($reqCtx);

        $users = $this->findUsers($reqCtx);
        $serializer->serialize($users);

        $this->assertSame($users, $serializer->get());

    }

    public function testSerializeScalar () {

        $reqCtx = new RequestContext();
        $serializer = new Serializer($reqCtx);

        $string = 'qwe';
        $int = 123;
        $float = 123.456;
        $true = true;
        $false = false;
        $null = NULL;

        $this->assertSame($string, $serializer->serialize($string)->get());
        $this->assertSame($int, $serializer->serialize($int)->get());
        $this->assertSame($float, $serializer->serialize($float)->get());
        $this->assertSame($true, $serializer->serialize($true)->get());
        $this->assertSame($false, $serializer->serialize($false)->get());
        $this->assertSame($null, $serializer->serialize($null)->get());

    }


}
