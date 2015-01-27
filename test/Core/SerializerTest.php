<?php

namespace Core;

use Core\Context\FindQueryContext;
use Core\Context\RequestContext;
use Core\Field\KeyPath;
use Core\Filter\StringFilter;
use Core\Model\Company;
use Core\Model\Storage;
use Core\Model\User;

class SerializerTest extends TestCase {

    public static function setUpBeforeClass () {

        parent::setUpBeforeClass();

        self::resetDatabase(self::getApplicationContext());

    }

    public function testSerialize () {

        $company1 = new Company();
        $company1->setName('Bigsool');
        $user1 = new User();
        $user1->setEmail('thierry@bigsool.com');
        $user1->setPassword('qwe');
        $user1->setCompany($company1);
        $user1->setRegisterDate(new \DateTime());
        $user2 = new User();
        $user2->setEmail('julien@bigsool.com');
        $user2->setCompany($company1);
        $user2->setPassword('qwe');
        $user2->setRegisterDate(new \DateTime());
        $user3 = new User();
        $user3->setEmail('thomas@bigsool.com');
        $user3->setCompany($company1);
        $user3->setPassword('qwe');
        $user3->setRegisterDate(new \DateTime());
        $company1->setOwner($user3);

        $storage = new Storage();
        $storage->setUrl('http://www.amazon.com/');
        $storage->setCompany($company1);
        $storage->setLogin('login');
        $storage->setPassword('qwe');
        $storage->setUsedspace(0);
        $storage->setLastusedspaceupdate(new \DateTime());
        $storage->setIsoutofquota(false);
        $company1->setStorage($storage);
        $company1->addUser($user1);
        $company1->addUser($user2);
        $company1->addUser($user3);
        $result = [$user1, $user2, $user3];

        $registry = self::getApplicationContext()->getNewRegistry();
        $registry->save($user1);
        $registry->save($user2);
        $registry->save($user3);
        $registry->save($company1);
        $registry->save($storage);

        $reqCtx = new RequestContext();
        $reqCtx->setReturnedRootEntity('User');
        $reqCtx->setReturnedKeyPaths([new KeyPath('email'), new KeyPath('company.storage.url')]);
        $serializer = new Serializer($reqCtx);
        $result = $serializer->serialize($result)->getJSON();
        $resultExpected = [
            ['email' => 'thierry@bigsool.com', 'Company' => ['Storage' => ['url' => 'http://www.amazon.com/']]],
            ['email' => 'julien@bigsool.com', 'Company' => ['Storage' => ['url' => 'http://www.amazon.com/']]],
            ['email' => 'thomas@bigsool.com', 'Company' => ['Storage' => ['url' => 'http://www.amazon.com/']]]
        ];

        $this->assertEquals(json_encode($resultExpected), $result);


        $result = [$user1, $user2, $user3];
        $reqCtx->setReturnedKeyPaths([new KeyPath('email'), new KeyPath('company.users.password')]);
        $serializer = new Serializer($reqCtx);
        $result = $serializer->serialize($result)->getJSON();
        $resultExpected = [
            ['email'   => 'thierry@bigsool.com',
             'Company' => ['User' => [['password' => 'qwe'], ['password' => 'qwe'], ['password' => 'qwe']]]
            ],
            ['email'   => 'julien@bigsool.com',
             'Company' => ['User' => [['password' => 'qwe'], ['password' => 'qwe'], ['password' => 'qwe']]]
            ],
            ['email'   => 'thomas@bigsool.com',
             'Company' => ['User' => [['password' => 'qwe'], ['password' => 'qwe'], ['password' => 'qwe']]]
            ]
        ];

        $this->assertEquals(json_encode($resultExpected), $result);

        $result = $serializer->serialize("blibli")->get();

        $this->assertEquals($result, "blibli");

        $result = $serializer->serialize(true)->get();

        $this->assertEquals($result, "1");
    }

    public function testSerializeArray () {

        $array = ['key' => 'value',
                  [1      => 'un',
                   'deux' => 2,
                   ['sub sub array',
                    ['sub sub sub array' => 'valueeee']
                   ]
                  ],
                  'qwe'
        ];

        $reqCtx = new RequestContext();
        $serializer = new Serializer($reqCtx);
        $serializer->serialize($array);

        $this->assertSame($array, $serializer->get());

    }

    public function testSerializeReturnOnlyRequestedFields () {

        $company = new Company();
        $company->setName('Qwe SA');
        $user = new User();
        $user->setEmail('qw1@qwe.com');
        $user->setPassword('qWe');
        $user->setRegisterDate(new \DateTime());
        $user2 = new User();
        $user2->setEmail('qw2@qwe.com');
        $user2->setPassword('qw3');
        $user2->setRegisterDate(new \DateTime());
        $storage = new Storage();
        $storage->setUrl('qweeeeee.qwe.q.w.e');
        $storage->setLogin('login');
        $storage->setPassword('QwE');
        $storage->setUsedspace(0);
        $storage->setLastusedspaceupdate(new \DateTime());
        $storage->setIsoutofquota(false);

        $company->setStorage($storage);
        $storage->setCompany($company);
        $company->addUser($user);
        $company->addUser($user2);
        $user->setCompany($company);
        $user2->setCompany($company);
        $company->setOwner($user);
        $user->setOwnedCompany($company);

        $registry = self::getApplicationContext()->getNewRegistry();
        $registry->save($user);

        $reqCtx = new RequestContext();
        $reqCtx->setReturnedRootEntity('User');
        $reqCtx->setReturnedKeyPaths([
                                         new KeyPath('name'),
                                         new KeyPath('email'),
                                         new KeyPath('company.name'),
                                         new KeyPath('company.storage.url'),
                                         new KeyPath('company.storage.lastUsedSpaceUpdate'),
                                     ]);

        $serializer = new Serializer($reqCtx);

        $qryCtx = new FindQueryContext('User', $reqCtx);
        $qryCtx->addFilter(new StringFilter('User', 'usersFromCompany', 'company = :company'));
        $qryCtx->addKeyPath(new KeyPath('*'));
        $qryCtx->addKeyPath(new KeyPath('company'));
        $qryCtx->addKeyPath(new KeyPath('company.storage'));
        $qryCtx->setParams(['company' => $company]);
        $usersArray = $registry->find($qryCtx);

        $expected = [
            [
                'name'    => $user->getName(),
                'email'   => $user->getEmail(),
                'company' => [
                    'name'    => $company->getName(),
                    'storage' => [
                        'url'                 => $storage->getUrl(),
                        'lastUsedSpaceUpdate' => $storage->getLastUsedSpaceUpdate(),
                    ]
                ]
            ],
            [
                'name'    => $user2->getName(),
                'email'   => $user2->getEmail(),
                'company' => [
                    'name'    => $company->getName(),
                    'storage' => [
                        'url'                 => $storage->getUrl(),
                        'lastUsedSpaceUpdate' => $storage->getLastUsedSpaceUpdate(),
                    ]
                ]
            ]

        ];

        // several entities as object
        $serializer->serialize([$user, $user2]);
        $this->assertSame($expected, $serializer->get());

        // several entities as array
        $serializer->serialize($usersArray);
        $this->assertSame($expected, $serializer->get());

        // 1 entity as object
        $serializer->serialize($user);
        $this->assertSame($expected[0], $serializer->get());

        // 1 entity as array
        $serializer->serialize($usersArray[0]);
        $this->assertSame($expected[0], $serializer->get());

        // try without RootEntity
        $reqCtx->setReturnedRootEntity(NULL);
        $serializer->serialize($usersArray);
        $this->assertSame($usersArray, $serializer->get());

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
