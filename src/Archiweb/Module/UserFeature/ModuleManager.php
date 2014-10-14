<?php


namespace Archiweb\Module\UserFeature;

use Archiweb\Action\GenericAction as Action;
use Archiweb\Context\ActionContext;
use Archiweb\Context\ApplicationContext;
use Archiweb\Context\FindQueryContext;
use Archiweb\Controller;
use Archiweb\Error\ErrorManager;
use Archiweb\Expression\BinaryExpression;
use Archiweb\Expression\KeyPath;
use Archiweb\Expression\Parameter;
use Archiweb\Field\Field;
use Archiweb\Field\StarField;
use Archiweb\Filter\ExpressionFilter;
use Archiweb\Filter\FilterReference;
use Archiweb\Module\ModuleManager as AbstractModuleManager;
use Archiweb\Module\UserFeature\Helper as UserFeatureHelper;
use Archiweb\Operator\EqualOperator;
use Archiweb\Parameter\SafeParameter;
use Archiweb\Rule\SimpleRule;
use Archiweb\Validation\UserValidation;
use Symfony\Component\Routing\Route;


class ModuleManager extends AbstractModuleManager {

    /**
     * @param ApplicationContext $context
     */
    public function loadActions (ApplicationContext &$context) {

        $context->addAction(new Action('User', 'create', function (ActionContext $context) {

        }, function (ActionContext $context) {

            $params =
                ['name'      => ERR_PARAMS_INVALID,
                 'email'     => ERR_INVALID_PARAM_EMAIL,
                 'firstname' => ERR_PARAMS_INVALID,
                 'password'  => ERR_INVALID_PASSWORD,
                 'knowsFrom' => ERR_PARAMS_INVALID
                ];

            $errorManager = new ErrorManager('fr');
            $userValidation = new UserValidation();
            foreach ($params as $name => $errorCode) {
                $param = $context->getParam($name);
                $value = isset($param) ? $param->getValue() : NULL;
                $violations = $userValidation->validate($name, $value);
                if ($violations->count()) {
                    $errorManager->addError($errorCode, $name);
                }
                else {
                    $context->setParam($name, new SafeParameter($value));
                }
            }
            $errors = $errorManager->getErrors();
            if (!empty($errors)) {
                throw $errorManager->getFormattedError();
            }

        }, function (ActionContext $context) {

            /**
             * @var UserFeatureHelper $helper
             */
            $helper = $context->getApplicationContext()->getHelper('UserFeatureHelper');
            $params = $context->getParams(['name', 'email', 'firstname', 'password', 'knowsFrom']);
            $params['lang'] = new SafeParameter('fr');
            $helper->createUser($context, $params);

            return $context['user'];

        }));

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadFields (ApplicationContext &$context) {

        $context->addField(new StarField('User'));
        $context->addField(new Field('User', 'email'));
        $context->addField(new Field('User', 'password'));
        $context->addField(new Field('User', 'name'));

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadFilters (ApplicationContext &$context) {

        $expression = new BinaryExpression(new EqualOperator(), new KeyPath('*'), new Parameter(':authUser'));
        $context->addFilter(new ExpressionFilter('User', 'me', 'SELECT', $expression));

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadHelpers (ApplicationContext &$context) {

        $context->addHelper('UserFeatureHelper', new Helper());

    }

    public function loadRoutes (ApplicationContext &$context) {

        $context->addRoute('userCreate', new Route('/user/create',
                                                   ['controller' => new Controller($context->getAction('User',
                                                                                                       'create'))
                                                   ]));

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadRules (ApplicationContext &$context) {

        $context->addRule(new SimpleRule('UserMeRule', function (FindQueryContext $context) {

            if ($context instanceof FindQueryContext) {

                return $context->getEntity() == 'User' || in_array('User', $context->getJoinedEntities());

            }

        }, new FilterReference($context, 'User', 'mine')));

    }

}