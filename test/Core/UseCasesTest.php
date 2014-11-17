<?php

namespace Core;


use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Context\RequestContext;
use Core\Expression\BinaryExpression;
use Core\Expression\KeyPath as ExprKeyPath;
use Core\Expression\Parameter;
use Core\Field\KeyPath as FieldKeyPath;
use Core\Field\StarField;
use Core\Filter\ExpressionFilter;
use Core\Filter\StringFilter;
use Core\Model\Company;
use Core\Model\HostedProject;
use Core\Model\SharedHostedProject;
use Core\Model\Storage;
use Core\Model\User;
use Core\Operator\EqualOperator;
use Core\Operator\MemberOf;
use Core\Operator\OrOperator;
use Core\Parameter\SafeParameter;
use Core\Parameter\UnsafeParameter;
use Core\Rule\FieldRule;

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
        self::$appCtx->addField($hostedProjectStarField = new StarField('HostedProject'));
        self::$appCtx->addField(new StarField('Storage'));

        self::$appCtx->addFilter(new StringFilter('Storage', 'notOutOfQuota', 'isOutOfQuota = 0'));

        self::$appCtx->addFilter(new StringFilter('HostedProject', 'withSharedProject',
                                                  'sharedHostedProjects.participant = :authUser'));

        $binary =
            new BinaryExpression(new MemberOf(), new Parameter(':authUser'), new ExprKeyPath('creator.company.users'));
        self::$appCtx->addFilter(new ExpressionFilter('HostedProject', 'fromMyCompany', $binary));

        $binary = new BinaryExpression(new OrOperator(),
                                       new BinaryExpression(new MemberOf(), new Parameter(':authUser'),
                                                            new ExprKeyPath('creator.company.users', true)),
                                       new BinaryExpression(new EqualOperator(), new Parameter(':authUser'),
                                                            new ExprKeyPath('sharedHostedProjects.participant', true)));
        self::$appCtx->addFilter(new ExpressionFilter('HostedProject', 'accessibleProject', $binary));

        self::$appCtx->addRule(new FieldRule(self::$appCtx->getFieldByEntityAndName('HostedProject', '*'),
                                             self::$appCtx->getFilterByEntityAndName('HostedProject',
                                                                                     'accessibleProject')));

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

        $registry = ApplicationContext::getInstance()->getNewRegistry();
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

        $registry = ApplicationContext::getInstance()->getNewRegistry();
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

        $registry = ApplicationContext::getInstance()->getNewRegistry();

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

        $registry = ApplicationContext::getInstance()->getNewRegistry();
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
        $user->setKnowsFrom($params['knowsFrom']->getValue());
        $user->setRegisterDate(new \DateTime());
        $user->setLastLoginDate(new \DateTime());
        $user->setConfirmationKey(uniqid());
        $user->setSalt(uniqid());

        $registry->save($user);

        $actCtx['user'] = $user;

    }

    public function testListProjects () {

        $this->createSomeUsersAndProjects();

        $reqCtx = new RequestContext(self::$appCtx);
        $reqCtx->setParams([]);
        $reqCtx->setReturnedRootEntity('HostedProject');
        $reqCtx->setReturnedKeyPaths([new FieldKeyPath('*'), new FieldKeyPath('creator.company.storage')]);

        $actCtx = $reqCtx->getNewActionContext();
        $result = $this->getUser('User 1');
        $this->assertCount(1, $result);
        $actCtx['authUser'] = $result[0];
        $this->assertInstanceOf('\Core\Model\User', $actCtx['authUser']);
        $this->assertSame('User 1', $actCtx['authUser']->getName());

        $qryCtx = new FindQueryContext('HostedProject', $reqCtx);
        $qryCtx->setParams(['authUser' => $actCtx['authUser']]);
        $qryCtx->addFilter(self::$appCtx->getFilterByEntityAndName('Storage', 'notOutOfQuota'));

        $registry = self::$appCtx->getNewRegistry();
        $result = $registry->find($qryCtx);
        $dql = $registry->getLastExecutedQuery();

        $exceptedQry =
            '/^' .
            'SELECT hostedProject, hostedProjectCreatorCompanyStorage ' .
            'FROM \\\\Core\\\\Model\\\\HostedProject hostedProject ' .
            'INNER JOIN hostedProject\\.creator hostedProjectCreator ' .
            'INNER JOIN hostedProjectCreator\\.company hostedProjectCreatorCompany ' .
            'INNER JOIN hostedProjectCreatorCompany\\.storage hostedProjectCreatorCompanyStorage ' .
            'LEFT JOIN hostedProjectCreatorCompany\\.users hostedProjectCreatorCompanyUsers ' .
            'LEFT JOIN hostedProject\\.sharedHostedProjects hostedProjectSharedHostedProjects ' .
            'LEFT JOIN hostedProjectSharedHostedProjects\\.participant hostedProjectSharedHostedProjectsParticipant ' .
            'WHERE \\(\\(hostedProjectCreatorCompanyStorage\\.isOutOfQuota = \'0\'\\) ' .
            'AND \\(\\(:authUser_[0-9a-z]+ MEMBER OF hostedProjectCreatorCompany\\.users\\) ' .
            'OR \\(:authUser_[0-9a-z]+ = hostedProjectSharedHostedProjects\\.participant\\)\\)\\)' .
            '$/';

        $this->assertRegExp($exceptedQry, $dql);
        $this->assertCount(3, $result);

    }

    protected function createSomeUsersAndProjects () {

        $user1 = $this->createUser(['name' => 'User 1']);
        $company1 = $this->createCompany(['name' => 'Company 1']);
        $storage1 = $this->createStorage(['login' => 'Storage 1', 'url' => 'URL']);
        $this->setUserToCompany($user1, $company1, $storage1);

        $user2 = $this->createUser(['name' => 'User 2']);
        $company2 = $this->createCompany(['name' => 'Company 2']);
        $storage2 = $this->createStorage(['login' => 'Storage 2', 'url' => 'URL']);
        $this->setUserToCompany($user2, $company2, $storage2);

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
        $user->setKnowsFrom($this->d($params, 'knowsFrom'));
        $user->setRegisterDate(new \DateTime());
        $user->setLastLoginDate(new \DateTime());
        $user->setConfirmationKey(uniqid());
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
     * @return Company
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
     * @param array $params
     *
     * @return Storage
     */
    protected function createStorage (array $params = []) {

        $storage = new Storage();
        $storage->setUrl($this->d($params, 'url'));
        $storage->setLogin($this->d($params, 'login'));
        $storage->setPassword($this->d($params, 'password'));
        $storage->setUsedspace($this->d($params, 'usedSpace', 0));
        $storage->setIsoutofquota($this->d($params, 'isOutOfQuota', 0));
        $storage->setLastusedspaceupdate(new \DateTime());

        self::$appCtx->getNewRegistry()->save($storage);

        return $storage;

    }

    /**
     * @param User    $user
     * @param Company $company
     * @param Storage $storage
     */
    protected function setUserToCompany (User &$user, Company &$company, Storage &$storage) {

        $user->setCompany($company);
        $user->setOwnedCompany($company);
        $company->setOwner($user);
        $company->addUser($user);
        $company->setStorage($storage);
        $storage->setCompany($company);

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

        $findCtx = new FindQueryContext('User');
        $findCtx->addFilter(new StringFilter('User', '', 'name = "' . $name . '"'));
        $findCtx->addKeyPath(new FieldKeyPath('*'));
        $user = self::$appCtx->getNewRegistry()->find($findCtx, false);

        return $user;

    }

} 