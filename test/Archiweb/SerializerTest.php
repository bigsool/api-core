<?php

namespace Archiweb;

use Archiweb\Context\RequestContext;
use Archiweb\Field\KeyPath;
use Archiweb\Model\Company;
use Archiweb\Model\Storage;
use Archiweb\Model\User;

class SerializerTest extends TestCase {

    function testSerialize() {

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
        $result = [$user1,$user2,$user3];

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
                             ['email'=>'thierry@bigsool.com', 'Company' => ['Storage' => ['url' => 'http://www.amazon.com/']]],
                             ['email'=>'julien@bigsool.com', 'Company' => ['Storage' => ['url' => 'http://www.amazon.com/']]],
                             ['email'=>'thomas@bigsool.com', 'Company' => ['Storage' => ['url' => 'http://www.amazon.com/']]]
                          ];

        $this->assertEquals(json_encode($resultExpected),$result);


        $result = [$user1,$user2,$user3];
        $reqCtx->setReturnedKeyPaths([new KeyPath('email'), new KeyPath('company.users.password')]);
        $serializer = new Serializer($reqCtx);
        $result = $serializer->serialize($result)->getJSON();
        $resultExpected = [
            ['email'=>'thierry@bigsool.com', 'Company' => ['User' => [['password' => 'qwe'],['password' => 'qwe'],['password' => 'qwe']]]],
            ['email'=>'julien@bigsool.com', 'Company' => ['User' => [['password' => 'qwe'],['password' => 'qwe'],['password' => 'qwe']]]],
            ['email'=>'thomas@bigsool.com', 'Company' => ['User' => [['password' => 'qwe'],['password' => 'qwe'],['password' => 'qwe']]]]
        ];

        $this->assertEquals(json_encode($resultExpected),$result);

        $result = $serializer->serialize("blibli")->get();

        $this->assertEquals($result,"blibli");

        $result = $serializer->serialize(true)->get();

        $this->assertEquals($result,"1");
    }

}
