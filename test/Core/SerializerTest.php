<?php

namespace Core;

use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Context\RequestContext;
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
    protected static $usersArray;

    /**
     * @var array
     */
    protected static $expectedAccount;

    /**
     * @var array
     */
    protected static $expected;

    /**
     * @var array
     */
    protected static $expected2;

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
     * @var TestAccount
     */
    protected static $account1;

    /**
     * @var TestAccount
     */
    protected static $account2;

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
                'name'    => self::$user1->getName(),
                'email'   => self::$user1->getEmail(),
                'company' => [
                    'name'    => self::$company1->getName(),
                    'storage' => [
                        'url'                 => self::$storage->getUrl(),
                        'lastUsedSpaceUpdate' => self::$storage->getLastUsedSpaceUpdate(),
                    ]
                ]
            ],
            [
                'name'    => self::$user2->getName(),
                'email'   => self::$user2->getEmail(),
                'company' => [
                    'name'    => self::$company1->getName(),
                    'storage' => [
                        'url'                 => self::$storage->getUrl(),
                        'lastUsedSpaceUpdate' => self::$storage->getLastUsedSpaceUpdate(),
                    ]
                ]
            ]
        ];

        self::$expected2 = [
            [
                'name'    => self::$user1->getName(),
                'email'   => self::$user1->getEmail(),
                'company' => [
                    'name'    => self::$company1->getName(),
                    'storage' => [
                        'url'                 => self::$storage->getUrl(),
                        'lastUsedSpaceUpdate' => self::$storage->getLastUsedSpaceUpdate(),
                    ]
                ]
            ],
            [
            'name'    => self::$user3->getName(),
            'email'   => self::$user3->getEmail(),
            'company' => [
                'name'    => self::$company1->getName(),
                'storage' => [
                    'url'                 => self::$storage->getUrl(),
                    'lastUsedSpaceUpdate' => self::$storage->getLastUsedSpaceUpdate(),
                ]
            ]
        ],
            [
                'name'    => self::$user2->getName(),
                'email'   => self::$user2->getEmail(),
                'company' => [
                    'name'    => self::$company1->getName(),
                    'storage' => [
                        'url'                 => self::$storage->getUrl(),
                        'lastUsedSpaceUpdate' => self::$storage->getLastUsedSpaceUpdate(),
                    ]
                ]
            ]
        ];

        self::$expectedAccount = [
            [
                'name'    => self::$account1->getUser()->getName(),
                'email'   => self::$account1->getUser()->getEmail(),
                'company' => [
                    'name' => self::$account1->getCompany()->getName(),
                ],
                'storage' => [
                    'url'                 => self::$account1->getStorage()->getUrl(),
                    'lastUsedSpaceUpdate' => self::$account1->getStorage()->getLastUsedSpaceUpdate(),
                ]

            ],
            [
                'name'    => self::$account2->getUser()->getName(),
                'email'   => self::$account2->getUser()->getEmail(),
                'company' => [
                    'name' => self::$account2->getCompany()->getName(),
                ],
                'storage' => [
                    'url'                 => self::$account2->getStorage()->getUrl(),
                    'lastUsedSpaceUpdate' => self::$account2->getStorage()->getLastUsedSpaceUpdate(),
                ]

            ]

        ];

    }

    public function testSerialize () {

        $result = [self::$user1, self::$user2, self::$user3];

        $reqCtx = new RequestContext();
        $reqCtx->setReturnedRootEntity('TestUser');
        $reqCtx->setReturnedKeyPaths([new KeyPath('email'), new KeyPath('company.storage.url')]);
        $serializer = new Serializer($reqCtx);
        $result = $serializer->serialize($result)->getJSON();
        
        $resultExpected = [
            [
                'email'   => self::$user1->getEmail(),
                'company' => [
                    'storage' => [
                        'url'                 => self::$storage->getUrl(),
                    ]
                ]
            ],
            [
                'email'   => self::$user2->getEmail(),
                'company' => [
                    'storage' => [
                        'url'                 => self::$storage->getUrl(),
                    ]
                ]
            ],
            [
                'email'   => self::$user3->getEmail(),
                'company' => [
                    'storage' => [
                        'url'                 => self::$storage->getUrl(),
                    ]
                ]
            ],
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

    public function testSeveralEntitiesAsObject () {

        $reqCtx = new RequestContext();
        $reqCtx->setReturnedRootEntity('TestUser');
        $reqCtx->setReturnedKeyPaths([
                                         new KeyPath('name'),
                                         new KeyPath('email'),
                                         new KeyPath('company.name'),
                                         new KeyPath('company.storage.url'),
                                         new KeyPath('company.storage.lastUsedSpaceUpdate'),
                                     ]);

        $serializer = new Serializer($reqCtx);

        $serializer->serialize([self::$user1, self::$user2]);
        $this->assertSame(self::$expected, $serializer->get());

    }

    public function testSeveralEntitiesAsArray () {

        $reqCtx = new RequestContext();
        $reqCtx->setReturnedRootEntity('TestUser');
        $reqCtx->setReturnedKeyPaths([
                                         new KeyPath('name'),
                                         new KeyPath('email'),
                                         new KeyPath('company.name'),
                                         new KeyPath('company.storage.url'),
                                         new KeyPath('company.storage.lastUsedSpaceUpdate'),
                                     ]);

        $serializer = new Serializer($reqCtx);

        $serializer->serialize($this->getUserArray($reqCtx));
        $this->assertSame(self::$expected2, $serializer->get());

    }

    /**
     * @param RequestContext $reqCtx
     *
     * @return array
     * @throws Error\FormattedError
     */
    public function getUserArray (RequestContext $reqCtx) {

        $qryCtx = new FindQueryContext('TestUser', $reqCtx);
        $qryCtx->addFilter(new StringFilter('TestUser', 'usersFromCompany', 'company = :company'));
        $qryCtx->addKeyPath(new KeyPath('*'));
        $qryCtx->addKeyPath(new KeyPath('company'));
        $qryCtx->addKeyPath(new KeyPath('company.storage'));
        $qryCtx->setParams(['company' => self::$company1]);

        return ApplicationContext::getInstance()->getNewRegistry()->find($qryCtx);

    }

    public function testOneEntityAsObject () {

        $reqCtx = new RequestContext();
        $reqCtx->setReturnedRootEntity('TestUser');
        $reqCtx->setReturnedKeyPaths([
                                         new KeyPath('name'),
                                         new KeyPath('email'),
                                         new KeyPath('company.name'),
                                         new KeyPath('company.storage.url'),
                                         new KeyPath('company.storage.lastUsedSpaceUpdate'),
                                     ]);

        $serializer = new Serializer($reqCtx);

        $serializer->serialize(self::$user1);
        $this->assertSame(self::$expected[0], $serializer->get());

    }

    public function testOneEntityAsArray () {

        $reqCtx = new RequestContext();
        $reqCtx->setReturnedRootEntity('TestUser');
        $reqCtx->setReturnedKeyPaths([
                                         new KeyPath('name'),
                                         new KeyPath('email'),
                                         new KeyPath('company.name'),
                                         new KeyPath('company.storage.url'),
                                         new KeyPath('company.storage.lastUsedSpaceUpdate'),
                                     ]);

        $serializer = new Serializer($reqCtx);

        $serializer->serialize($this->getUserArray($reqCtx)[0]);
        $this->assertSame(self::$expected[0], $serializer->get());

    }

    public function testArrayWithoutKeyPath () {

        $reqCtx = new RequestContext();
        $reqCtx->setReturnedRootEntity('TestUser');
        /*$reqCtx->setReturnedKeyPaths([
                                         new KeyPath('name'),
                                         new KeyPath('email'),
                                         new KeyPath('company.name'),
                                         new KeyPath('company.storage.url'),
                                         new KeyPath('company.storage.lastUsedSpaceUpdate'),
                                     ]);*/

        $serializer = new Serializer($reqCtx);

        $reqCtx->setReturnedRootEntity(NULL);
        $serializer->serialize($this->getUserArray($reqCtx));
        $this->assertSame($this->getUserArray($reqCtx), $serializer->get());

    }

    public function testMagicalEntity () {

        $reqCtx = new RequestContext();
        $reqCtx->setReturnedRootEntity('TestUser');
        $reqCtx->setReturnedKeyPaths([
                                         new KeyPath('name'),
                                         new KeyPath('email'),
                                         new KeyPath('company.name'),
                                         new KeyPath('storage.url'),
                                         new KeyPath('storage.lastUsedSpaceUpdate'),
                                     ]);

        $serializer = new Serializer($reqCtx);

        $serializer->serialize([self::$account1, self::$account2]);
        $this->assertSame(self::$expectedAccount, $serializer->get());

        $serializer->serialize(self::$account1);
        $this->assertSame(self::$expectedAccount[0], $serializer->get());

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
