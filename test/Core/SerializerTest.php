<?php

namespace Core;

use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Context\RequestContext;
use Core\Field\Aggregate;
use Core\Field\RelativeField;
use Core\Model\TestCompany;
use Core\Model\TestStorage;
use Core\Model\TestUser;

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
            'success' => true,
            'data'    => [
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
            ]
        ];

        self::$expectedWithAggregate = [
            'success' => true,
            'data'    => [
                [
                    self::$expected['data'][2],
                    'TestUserCount' => '1'
                ],
                [
                    self::$expected['data'][0],
                    'TestUserCount' => '1'
                ],
                [
                    self::$expected['data'][1],
                    'TestUserCount' => '1'
                ],
            ]
        ];

    }

    public function testSerialize () {

        $reqCtx = new RequestContext();

        $emailKP = new RelativeField('email');
        $companyIdKP = new RelativeField('company.name');
        $storageIdKP = new RelativeField('company.storage.id');
        $storageUrlKP = new RelativeField('company.storage.url');

        $reqCtx->setReturnedFields([$emailKP, $companyIdKP, $storageIdKP, $storageUrlKP]);

        $users = $this->findUsers($reqCtx);

        $serializer = new Serializer(new ActionContext($reqCtx));

        $serializer->serialize($users);
        $this->assertSame(self::$expected, $serializer->get());

        $reqCtx = new RequestContext();

        // TODO: this test has no sens. We cannot ask for a aggregate field. We may ask for a calculated field.
        /*
        $emailKP = new RelativeField('email');
        $nbUsersKP = new RelativeField(new Aggregate('count', ['*']));
        $companyIdKP = new RelativeField('company.name');
        $storageIdKP = new RelativeField('company.storage.id');
        $storageUrlKP = new RelativeField('company.storage.url');

        $reqCtx->setReturnedFields([$nbUsersKP, $emailKP, $companyIdKP, $storageIdKP, $storageUrlKP]);

        $users = $this->findUsers($reqCtx);

        $serializer = new Serializer(new ActionContext($reqCtx));

        $serializer->serialize($users);

        $this->assertSame(self::$expectedWithAggregate, $serializer->get());

        $this->assertSame(json_encode(self::$expectedWithAggregate), $serializer->getJSON());*/

    }

    /**
     * @param RequestContext $reqCtx
     *
     * @return array
     */
    private function findUsers (RequestContext $reqCtx) {

        $qryCtx = new FindQueryContext('TestUser', $reqCtx);

        $keyPaths = $reqCtx->getReturnedFields();
        foreach ($keyPaths as $keyPath) {
            $qryCtx->addField($keyPath, $keyPath->getAlias());
        }

        return ApplicationContext::getInstance()->getNewRegistry()->find($qryCtx);

    }

    /**
     * @expectedException \Exception
     */
    public function testArrayWithoutKeyPath () {

        $reqCtx = new RequestContext();

        $serializer = new Serializer(new ActionContext($reqCtx));

        $users = $this->findUsers($reqCtx);
        $serializer->serialize($users);

        $this->assertSame($users, $serializer->get());

    }

    public function testSerializeScalar () {

        $reqCtx = new RequestContext();
        $serializer = new Serializer(new ActionContext($reqCtx));

        $string = 'qwe';
        $int = 123;
        $float = 123.456;
        $true = true;
        $false = false;
        $null = NULL;

        $this->assertSame(['success' => true, 'data' => $string], $serializer->serialize($string)->get());
        $this->assertSame(['success' => true, 'data' => $int], $serializer->serialize($int)->get());
        $this->assertSame(['success' => true, 'data' => $float], $serializer->serialize($float)->get());
        $this->assertSame(['success' => true, 'data' => $true], $serializer->serialize($true)->get());
        $this->assertSame(['success' => true, 'data' => $false], $serializer->serialize($false)->get());
        $this->assertSame(['success' => true, 'data' => $null], $serializer->serialize($null)->get());

    }

    public function testV1ListHostedProject () {

        $result = json_decode('{
[
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
', true);

        $reqCtx = new RequestContext();
        $serializer = new Serializer(new ActionContext($reqCtx));
        $serializer->setInProxyMode(true);

        $this->assertSame(['success' => true, 'data' => $result], $serializer->serialize($result)->get());

    }

    public function testDateTime () {

        $data = [$datetime = new \DateTime()];
        $serializer = new Serializer(new ActionContext(new RequestContext()));

        $this->assertSame(['success' => true, 'data' => [$datetime->format($datetime::ISO8601)]],
                          $serializer->serialize($data)->get());

    }

    public function testProxyMode () {

        $serializer = new Serializer(new ActionContext(new RequestContext()));
        $serializer->setInProxyMode(true);
        $this->assertTrue($serializer->isInProxyMode());
        $serializer->setInProxyMode(false);
        $this->assertFalse($serializer->isInProxyMode());

    }

}
