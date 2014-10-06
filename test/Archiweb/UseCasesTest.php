<?php

namespace Archiweb;


use Archiweb\Context\ActionContext;
use Archiweb\Context\ApplicationContext;
use Archiweb\Context\FindQueryContext;
use Archiweb\Context\QueryContext;
use Archiweb\Context\RequestContext;
use Archiweb\Expression\BinaryExpression;
use Archiweb\Expression\KeyPath;
use Archiweb\Expression\Parameter;
use Archiweb\Field\StarField;
use Archiweb\Filter\ExpressionFilter;
use Archiweb\Filter\StringFilter;
use Archiweb\Model\Company;
use Archiweb\Model\HostedProject;
use Archiweb\Model\SharedHostedProject;
use Archiweb\Model\Storage;
use Archiweb\Model\User;
use Archiweb\Operator\MemberOf;
use Archiweb\Parameter\SafeParameter;
use Archiweb\Parameter\UnsafeParameter;
use Archiweb\Rule\SimpleRule;

class UseCasesTest extends TestCase {

    /**
     * @var ApplicationContext
     */
    protected static $appCtx;

    public static function  setUpBeforeClass () {

        parent::setUpBeforeClass();

        self::$appCtx = self::getApplicationContext();
        self::resetDatabase(self::$appCtx);

        self::$appCtx->addField(new StarField('User'));
        self::$appCtx->addField(new StarField('HostedProject'));

        self::$appCtx->addFilter(new StringFilter('HostedProject', 'withSharedProject',
                                                  'self.sharedHostedProjects.participant = :authUser', 'SELECT'));

        $binary =
            new BinaryExpression(new MemberOf(), new Parameter(':authUser'), new KeyPath('creator.company.users'));
        self::$appCtx->addFilter(new ExpressionFilter('HostedProject', 'fromMyCompany', 'SELECT', $binary));
        self::$appCtx->addRule(new SimpleRule('fromMyCompanyRule', function (QueryContext $context) {

            if ($context instanceof FindQueryContext) {

                // TODO: il faut aussi gérer dans le cas où HostedProject fait partie des joins
                return $context->getEntity() == 'HostedProject';

            }

            return false;

        }, self::$appCtx->getFilterByEntityAndName('HostedProject', 'fromMyCompany')));

    }

    public function testCreateUser () {

        $reqCtx = new RequestContext(self::$appCtx);
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

    public function testListProjects () {

        $this->createSomeUsersAndProjects();

        $reqCtx = new RequestContext(self::$appCtx);
        $reqCtx->setParams([]);

        $actCtx = $reqCtx->getNewActionContext();
        $result = $this->getUser('User 1');
        $this->assertCount(1, $result);
        $actCtx['authUser'] = $result[0];
        $this->assertInstanceOf('\Archiweb\Model\User', $actCtx['authUser']);
        $this->assertSame('User 1', $actCtx['authUser']->getName());

        $qryCtx = new FindQueryContext(self::$appCtx, 'HostedProject');
        $qryCtx->addKeyPath(new KeyPath('*'));
        $qryCtx->setParams(['authUser' => $actCtx['authUser']]);

        $registry = self::$appCtx->getNewRegistry();
        $result = $registry->find($qryCtx);

    }

    protected function createSomeUsersAndProjects () {

        $user1 = $this->createUser(['name' => 'User 1']);
        $company1 = $this->createCompany(['name' => 'Company 1']);
        $this->setUserToCompany($user1, $company1);

        $user2 = $this->createUser(['name' => 'User 2']);
        $company1 = $this->createCompany(['name' => 'Company 2']);
        $this->setUserToCompany($user2, $company1);

        $this->createProject('Projet de 1', $user1);
        $this->createProject('Projet de 1 partagé avec 2', $user1, [$user2]);
        $this->createProject('Projet de 2', $user2);
        $this->createProject('Projet de 2 partagé avec 1', $user2, [$user1]);

    }

    /**
     * @param array $params
     *
     * @return User
     */
    protected function createUser (array $params = []) {

        $user = new User();
        $user->setEmail($this->d($params, 'email', uniqid()));
        $user->setPassword($this->d($params, 'password'));
        $user->setName($this->d($params, 'name'));
        $user->setFirstname($this->d($params, 'firstname'));
        $user->setLang($this->d($params, 'lang'));
        $user->setKnowsfrom($this->d($params, 'knowsFrom'));
        $user->setRegisterDate(new \DateTime());
        $user->setLastLoginDate(new \DateTime());
        $user->setConfirmationkey(uniqid());
        $user->setSalt(uniqid());

        self::$appCtx->getNewRegistry()->save($user);

        return $user;

    }

    /**
     * @param array  $arr
     * @param        $key
     * @param string $default
     *
     * @return string
     */
    protected function d (array &$arr, $key, $default = '') {

        return isset($arr[$key]) ? $arr[$key] : $default;

    }

    /**
     * @param array $params
     *
     * @return User
     */
    protected function createCompany (array $params = []) {

        $company = new Company();
        $company->setName($this->d($params, 'name'));
        $company->setAddress($this->d($params, 'address'));
        $company->setZipCode($this->d($params, 'zipCode'));
        $company->setCity($this->d($params, 'city'));
        $company->setState($this->d($params, 'state'));
        $company->setCountry($this->d($params, 'country'));
        $company->setTel($this->d($params, 'tel'));
        $company->setFax($this->d($params, 'fax'));
        $company->setTva($this->d($params, 'tva'));

        self::$appCtx->getNewRegistry()->save($company);

        return $company;

    }

    /**
     * @param User    $user
     * @param Company $company
     */
    protected function setUserToCompany (User &$user, Company &$company) {

        $user->setCompany($company);
        $user->setOwnedCompany($company);
        $company->setOwner($user);
        $company->addUser($user);

        self::$appCtx->getNewRegistry()->save($user);

    }

    /**
     * @param        $name
     * @param User   $owner
     * @param User[] $participants
     *
     * @return HostedProject
     */
    protected function createProject ($name, User $owner, array $participants = []) {

        $project = new HostedProject();
        $project->setName($name);
        $project->setClientNameCreator('');
        $project->setClientVersionCreator('');
        $project->setCreationDate(new \DateTime());
        $project->setCreator($owner);
        $owner->addHostedProject($project);
        $project->setIsSynchronizable(true);
        $project->setIsUploading(false);
        $project->setLastModificationDate(new \DateTime());
        $project->setPatchId('');
        $project->setUUIDCreator('');

        self::$appCtx->getNewRegistry()->save($project);

        foreach ($participants as $participant) {
            $shp = new SharedHostedProject();
            $shp->setPermission('');
            $shp->setHostedProject($project);
            $shp->setParticipant($participant);
            $participant->addSharedHostedProject($shp);
            $project->addSharedHostedProject($shp);
        }

        self::$appCtx->getNewRegistry()->save($project);

        return $project;

    }

    /**
     * @param $name
     *
     * @return array
     */
    protected function getUser ($name) {

        $findCtx = new FindQueryContext(self::$appCtx, 'User');
        $findCtx->addFilter(new StringFilter('User', '', 'name = "' . $name . '"', 'SELECT'));
        $findCtx->addKeyPath(new KeyPath('*'));
        $user = self::$appCtx->getNewRegistry()->find($findCtx, false);

        return $user;

    }

} 