<?php


namespace Core\Module\UserFeature;


use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Field\KeyPath as FieldKeyPath;
use Core\Filter\StringFilter;
use Core\Model\User;
use Core\Parameter\Parameter;
use Core\Parameter\SafeParameter;

class Helper {

    /**
     * @param ActionContext $actCtx
     * @param Parameter[]   $params
     */
    public function createUser (ActionContext $actCtx, array $params) {

        $registry = ApplicationContext::getInstance()->getNewRegistry();

        $salt = self::createSalt();
        $params['password'] = new SafeParameter(self::encryptPassword($salt, $params['password']->getValue()));

        $user = new User();

        $user->setEmail($params['email']);
        $user->setPassword($params['password']);
        $user->setName($params['name']);
        $user->setFirstname($params['firstname']);
        $user->setLang($params['lang']);
        $user->setKnowsFrom($params['knowsFrom']);
        $user->setRegisterDate(new \DateTime());
        $user->setConfirmationKey(uniqid());
        $user->setSalt($salt);

        $registry->save($user);

        $actCtx['user'] = $user;

    }

    /**
     * @param ActionContext $actCtx
     * @param Parameter[]   $params
     */
    public function updateUser (ActionContext $actCtx, array $params) {

        $registry = ApplicationContext::getInstance()->getNewRegistry();

        $qryCtx = new FindQueryContext('User', $actCtx->getRequestContext());

        $qryCtx->addKeyPath(new FieldKeyPath('*'));

        $qryCtx->setParams(['id' => $params['id']->getValue()]);

        $qryCtx->addFilter(new StringFilter('User', '', 'id = :id'));
        $result = $registry->find($qryCtx, false);

        $user = $result[0];

        if (isset($params['email'])) {
            $user->setEmail($params['email']);
        }
        if (isset($params['password'])) {
            $user->setPassword($params['password']);
        }
        if (isset($params['name'])) {
            $user->setName($params['name']);
        }
        if (isset($params['firstname'])) {
            $user->setFirstname($params['firstname']);
        }

        $registry->save($user);

        $actCtx['user'] = $user;

    }

    /**
     * @return string
     */
    public static function createSalt () {

        return uniqid('', true);

    }

    /**
     * @param string $salt
     * @param string $password
     *
     * @return string
     */
    public static function encryptPassword ($salt, $password) {

        $hash = $salt . $password;
        for ($i = 0; $i < 3004; ++$i) {
            $hash = hash('sha512', $salt . $hash);
        }

        return $hash;

    }

} 