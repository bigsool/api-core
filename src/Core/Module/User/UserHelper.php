<?php


namespace Core\Module\User;


use Core\Context\ApplicationContext;
use Core\Helper\GenericHelper;
use Core\Model\User;

class UserHelper extends GenericHelper {

    public function __construct(ApplicationContext $applicationContext) {

        parent::__construct($applicationContext, 'TestUser');

    }

    public function create(array $values) {

        /**
         * @var User $model
         */
        $model = parent::create($values);
        $model->setCreationDate(new \DateTime());

        return $model;

    }

}