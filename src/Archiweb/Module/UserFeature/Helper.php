<?php


namespace Archiweb\Module\UserFeature;


use Archiweb\Context\ActionContext;

class Helper {

    public function createUser (ActionContext $actCtx, array $params) {

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