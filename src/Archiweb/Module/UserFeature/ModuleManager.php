<?php


namespace Archiweb\Module\UserFeature;

use Archiweb\Action\GenericAction as Action;
use Archiweb\Context\ActionContext;
use Archiweb\Context\ApplicationContext;
use Archiweb\Context\FindQueryContext;
use Archiweb\Controller;
use Archiweb\Expression\BinaryExpression;
use Archiweb\Expression\KeyPath;
use Archiweb\Expression\Parameter;
use Archiweb\Field\Field;
use Archiweb\Field\StarField;
use Archiweb\Filter\ExpressionFilter;
use Archiweb\Filter\FilterReference;
use Archiweb\Module\ModuleManager as AbstractModuleManager;
use Archiweb\Operator\EqualOperator;
use Archiweb\Rule\SimpleRule;
use Symfony\Component\Routing\Route;


class ModuleManager extends AbstractModuleManager {

    /**
     * @param ApplicationContext $context
     */
    public function loadActions (ApplicationContext &$context) {

        $context->addAction(new Action('User', 'create', function (ActionContext $context) {

        }, function (ActionContext $context) {

        }, function (ActionContext $context) {

            return ['user'=>'qwe'];

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

            return $context->getEntity() == 'User' || in_array('User', $context->getJoinedEntities());

        }, new FilterReference($context, 'User', 'mine')));

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadHelpers(ApplicationContext &$context) {

        $context->addHelper('UserFeatureHelper', new Helper());

    }

}