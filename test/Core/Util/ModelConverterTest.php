<?php


namespace Core\Util;


use Core\Context\FindQueryContext;
use Core\Context\RequestContext;
use Core\Field\RelativeField;
use Core\Filter\StringFilter;
use Core\Model\TestCompany;
use Core\Model\TestUser;
use Core\TestCase;

class ModelConverterTest extends TestCase {

    public static function setUpBeforeClass () {

        parent::setUpBeforeClass();

        $ctx = self::getApplicationContext();

        self::resetDatabase($ctx);

    }

    public function testToArray () {

        // AppCtx must be initialized
        $appCtx = $this->getApplicationContext();

        $owner = new TestUser();
        $owner->setEmail('owner@company.com');
        $owner->setPassword('qwe');
        $owner->setRegisterDate(new \DateTime());

        $company = new TestCompany();
        $company->setName('owned company');

        $company->setOwner($owner);
        $owner->setOwnedCompany($company);
        $company->addUser($owner);
        $owner->setCompany($company);

        foreach (range(1, 5) as $subUserNb) {
            $subUser = new TestUser();
            $subUser->setEmail("subUser{$subUserNb}@company.com");
            $subUser->setPassword('qwe' . $subUserNb);
            $subUser->setRegisterDate(new \DateTime());
            $company->addUser($subUser);
            $subUser->setCompany($company);
        }

        $saveRegistry = $appCtx->getNewRegistry();
        $saveRegistry->save($owner);

        $reqCtx = new RequestContext();
        $qryCtx = new FindQueryContext('TestUser', $reqCtx);
        $filter = new StringFilter('TestUser', 'companyOwnerOnly', 'ownedCompany.id = ' . $company->getId());
        $qryCtx->addFilter($filter);
        $filter->setAliasForEntityToUse('testUser');

        $qryCtx->addField(new RelativeField('*'));
        $qryCtx->addField(new RelativeField('company.users.*'));

        $findRegistry = $appCtx->getNewRegistry();
        $findRegistry->find($qryCtx);

        $modelConverter = new ModelConverter();

        $result = $modelConverter->toArray($owner,
                                           ['email',
                                            'company.id',
                                            'company.users.password',
                                            'company.users.email',
                                            'company.users.company.name',
                                            'password',
                                            'company.name'
                                           ]);
        $expected = [
            'email'    => 'owner@company.com',
            'password' => 'qwe',
            'company'  => [
                'id'    => 1,
                'name'  => 'owned company',
                'users' => [
                    [
                        'password' => 'qwe',
                        'email'    => 'owner@company.com',
                        'company'  => [
                            'name' => 'owned company',
                        ],
                    ],
                    [
                        'password' => 'qwe1',
                        'email'    => 'subUser1@company.com',
                        'company'  => [],
                    ],
                    [
                        'password' => 'qwe2',
                        'email'    => 'subUser2@company.com',
                        'company'  => [],
                    ],
                    [
                        'password' => 'qwe3',
                        'email'    => 'subUser3@company.com',
                        'company'  => [],
                    ],
                    [
                        'password' => 'qwe4',
                        'email'    => 'subUser4@company.com',
                        'company'  => [],
                    ],
                    [
                        'password' => 'qwe5',
                        'email'    => 'subUser5@company.com',
                        'company'  => [],
                    ],
                ],
            ],
        ];

        $this->assertEquals($expected, $result);

    }

}