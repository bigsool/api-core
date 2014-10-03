<?php

namespace Archiweb;


use Archiweb\Context\ActionContext;
use Archiweb\Context\ApplicationContext;
use Archiweb\Context\RequestContext;
use Archiweb\Model\Company;
use Archiweb\Model\Storage;
use Archiweb\Model\User;
use Archiweb\Parameter\SafeParameter;
use Archiweb\Parameter\UnsafeParameter;

class UseCasesTest extends TestCase {

    /**
     * @var ApplicationContext
     */
    protected $appCtx;

    public function setUp () {

        $this->appCtx = $this->getApplicationContext();
        $this->resetDatabase($this->appCtx);

    }

    public function testCreateUser () {

        $reqCtx = new RequestContext($this->appCtx);
        $reqCtx->setParams(['user'    => ['email'     => 'qwe@qwe.come',
                                          'password'  => 'qwe',
                                          'name'      => 'Ferrier',
                                          'firstname' => 'Julien',
                                          'lang'      => 'fr',
                                          'knowsFrom' => 'Salon Batimat'
        ],
                            'company' => ['name'    => 'bigsool',
                                          'address' => "310 Rue du vallon\nImmeuble Atlas A\nSophia Antipolis",
                                          'zipCode' => '06560',
                                          'city'    => 'Valbonne',
                                          'state'   => '',
                                          'country' => 'France',
                                          'tel'     => '0427868460',
                                          'fax'     => 'aucun !!',
                                          'tva'     => 'FR4587788962'
                            ]
                           ]);

        $actCtx = $reqCtx->getNewActionContext();

        $this->createUserWithCompanyAction($actCtx);

    }

    protected function createUserWithCompanyAction (ActionContext $actCtx) {

        $actCtxCompany = clone $actCtx;
        $params = [];
        $this->assertFalse($actCtxCompany->getParam('company')->isSafe());
        foreach ($actCtxCompany->getParam('company')->getValue() as $key => $value) {
            $params[$key] = new UnsafeParameter($value);
        }
        $actCtxCompany->setParams($params);
        $this->createCompanyAction($actCtxCompany);

        $actCtxUser = clone $actCtx;
        $params = [];
        $this->assertFalse($actCtxUser->getParam('user')->isSafe());
        foreach ($actCtxUser->getParam('user')->getValue() as $key => $value) {
            $params[$key] = new UnsafeParameter($value);
        }
        $actCtxUser->setParams($params);
        $this->createUserAction($actCtxUser);

        $actCtxUser['user']->setCompany($actCtxCompany['company']);
        $actCtxUser['user']->setOwnedCompany($actCtxCompany['company']);
        $actCtxCompany['company']->addUser($actCtxUser['user']);
        $actCtxCompany['company']->setOwner($actCtxUser['user']);

        $registry = $actCtx->getApplicationContext()->getNewRegistry();
        $registry->save($actCtxUser['user']);

    }

    protected function createCompanyAction (ActionContext $actCtx) {

        $params = [];
        foreach ($actCtx->getParams() as $key => $value) {
            $this->assertFalse($value->isSafe());
            $params[$key] = new SafeParameter($value->getValue());
        }
        $actCtx->setParams($params);

        $this->companyHelper($actCtx);
        $this->storageHelper($actCtx, $actCtx['company']->getName());

        $actCtx['company']->setStorage($actCtx['storage']);
        $actCtx['storage']->setCompany($actCtx['company']);

    }

    protected function companyHelper (ActionContext $actCtx) {

        $registry = $actCtx->getApplicationContext()->getNewRegistry();
        $params = $actCtx->getParams();
        foreach ($params as $param) {
            $this->assertTrue($param->isSafe());
        }

        $company = new Company();
        $company->setName($params['name']->getValue());
        $company->setAddress($params['address']->getValue());
        $company->setZipCode($params['zipCode']->getValue());
        $company->setCity($params['city']->getValue());
        $company->setState($params['state']->getValue());
        $company->setCountry($params['country']->getValue());
        $company->setTel($params['tel']->getValue());
        $company->setFax($params['fax']->getValue());
        $company->setTva($params['tva']->getValue());

        $registry->save($company);

        $actCtx['company'] = $company;

    }

    protected function storageHelper (ActionContext $actCtx, $prefix) {

        $registry = $actCtx->getApplicationContext()->getNewRegistry();

        $storage = new Storage();
        $storage->setUrl(uniqid($prefix));
        $storage->setLogin('IAM LOGIN');
        $storage->setPassword('IAM PASSWORD');
        $storage->setUsedspace(0);
        $storage->setIsoutofquota(false);
        $storage->setLastusedspaceupdate(new \DateTime());

        $registry->save($storage);

        $actCtx['storage'] = $storage;

    }

    protected function createUserAction (ActionContext $actCtx) {

        $params = [];
        foreach ($actCtx->getParams() as $key => $value) {
            $this->assertFalse($value->isSafe());
            $params[$key] = new SafeParameter($value->getValue());
        }
        $actCtx->setParams($params);

        $this->userHelper($actCtx);

    }

    protected function userHelper (ActionContext $actCtx) {

        $registry = $actCtx->getApplicationContext()->getNewRegistry();
        $params = $actCtx->getParams();
        foreach ($params as $param) {
            $this->assertTrue($param->isSafe());
        }

        $user = new User();
        $user->setEmail($params['email']->getValue());
        $user->setPassword($params['password']->getValue());
        $user->setName($params['name']->getValue());
        $user->setFirstname($params['firstname']->getValue());
        $user->setLang($params['lang']->getValue());
        $user->setKnowsfrom($params['knowsFrom']->getValue());
        $user->setRegisterDate(new \DateTime());
        $user->setLastLoginDate(new \DateTime());
        $user->setConfirmationkey(uniqid());
        $user->setSalt(uniqid());

        $registry->save($user);

        $actCtx['user'] = $user;

    }

} 