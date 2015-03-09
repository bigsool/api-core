<?php

namespace Core;

use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Context\RequestContext;
use Core\Field\Aggregate;
use Core\Field\KeyPath;
use Core\Model\TestCompany;
use Core\Model\TestStorage;
use Core\Model\TestUser;
use Core\Validation\Parameter\DateTime;

class SerializerTest extends TestCase {

    /**
     * @var array
     */
    protected static $expected;

    /**
     * @var array
     */
    protected static $expectedWithAggregate;

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
                'email'   => self::$user1->getEmail(),
                'company' => [
                    'name'    => self::$company1->getName(),
                    'storage' => [
                        'id'  => self::$storage->getId(),
                        'url' => self::$storage->getUrl(),
                    ]
                ]
            ],
            [
                'email'   => self::$user3->getEmail(),
                'company' => [
                    'name'    => self::$company1->getName(),
                    'storage' => [
                        'id'  => self::$storage->getId(),
                        'url' => self::$storage->getUrl(),
                    ]
                ]
            ],
            [
                'email'   => self::$user2->getEmail(),
                'company' => [
                    'name'    => self::$company1->getName(),
                    'storage' => [
                        'id'  => self::$storage->getId(),
                        'url' => self::$storage->getUrl(),
                    ]
                ],
            ],
        ];

        self::$expectedWithAggregate = [
            [
                self::$expected[2],
                'TestUserCount' => '1'
            ],
            [
                self::$expected[0],
                'TestUserCount' => '1'
            ],
            [
                self::$expected[1],
                'TestUserCount' => '1'
            ],
        ];

    }

    public function testSerialize () {

        $reqCtx = new RequestContext();

        $emailKP = new KeyPath('email');
        $companyIdKP = new KeyPath('company.name');
        $storageIdKP = new KeyPath('company.storage.id');
        $storageUrlKP = new KeyPath('company.storage.url');

        $reqCtx->setReturnedKeyPaths([$emailKP, $companyIdKP, $storageIdKP, $storageUrlKP]);

        $users = $this->findUsers($reqCtx);

        $serializer = new Serializer($reqCtx);

        $serializer->serialize($users);
        $this->assertSame(self::$expected, $serializer->get());

        $reqCtx = new RequestContext();

        $emailKP = new KeyPath('email');
        $nbUsersKP = new Aggregate('count', ['*']);
        $companyIdKP = new KeyPath('company.name');
        $storageIdKP = new KeyPath('company.storage.id');
        $storageUrlKP = new KeyPath('company.storage.url');

        $reqCtx->setReturnedKeyPaths([$nbUsersKP, $emailKP, $companyIdKP, $storageIdKP, $storageUrlKP]);

        $users = $this->findUsers($reqCtx);

        $serializer = new Serializer($reqCtx);

        $serializer->serialize($users);

        $this->assertSame(self::$expectedWithAggregate, $serializer->get());

        $this->assertSame(json_encode(self::$expectedWithAggregate), $serializer->getJSON());

    }

    private function findUsers ($reqCtx) {

        $qryCtx = new FindQueryContext('TestUser', $reqCtx);

        $keyPaths = $reqCtx->getReturnedKeyPaths();
        foreach ($keyPaths as $keyPath) {
            $qryCtx->addKeyPath($keyPath, $keyPath->getAlias());
        }

        return ApplicationContext::getInstance()->getNewRegistry()->find($qryCtx);

    }

    /**
     * @expectedException \Exception
     */
    public function testArrayWithoutKeyPath () {

        $reqCtx = new RequestContext();

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

    public function testV1ListHostedProject () {

        $result = json_decode('{
  "data": [
    {
      "id": "4543434357486343",
      "creator": "3",
      "name": "Qwe",
      "creationDate": 1424354103,
      "patchId": null,
      "lastModificationDate": 1424354103,
      "clientNameCreator": "archipad-enterprise",
      "clientVersionCreator": "3.3.1",
      "UUIDCreator": "CD129869-933C-4B37-B234-7F36B8B0FBC7",
      "isUploading": "1",
      "isSynchronizable": "1",
      "isExternalProject": "0",
      "permission": null,
      "expired": false,
      "uploading": true,
      "path": "3-54e1f9c02bc99-qwe\/projects\/0df4c214738b12eb"
    },
    {
      "id": "56457567867865341",
      "creator": "3",
      "name": "Qwe3",
      "creationDate": 1424360032,
      "patchId": null,
      "lastModificationDate": 1424360032,
      "clientNameCreator": "archipad-enterprise",
      "clientVersionCreator": "3.3.1",
      "UUIDCreator": "CD129869-933C-4B37-B234-7F36B8B0FBC7",
      "isUploading": "1",
      "isSynchronizable": "1",
      "isExternalProject": "0",
      "permission": null,
      "expired": false,
      "uploading": true,
      "path": "3-54e1f9c02bc99-qwe\/projects\/1b962fb018c97675"
    }
  ]
}', true);

        $reqCtx = new RequestContext();
        $serializer = new Serializer($reqCtx);
        $serializer->setInProxyMode(true);

        $this->assertSame($result, $serializer->serialize($result)->get());

    }

    public function testDateTime() {

        $data = [$datetime = new \DateTime()];
        $serializer = new Serializer(new RequestContext());

        $this->assertSame([$datetime->format($datetime::ISO8601)], $serializer->serialize($data)->get());

    }

}
