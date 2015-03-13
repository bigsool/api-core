<?php


namespace Core\Module;


use Core\Action\GenericAction;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;

abstract class API {

    abstract public function load ();

    /**
     * @param string   $path
     * @param string   $actionName
     * @param string[] $auth
     * @param string[] $defaultFields
     * @param string[] $requiredParameters
     */
    public function defineRoute ($path, $actionName, $auth, array $defaultFields = [], array $requiredParameters = []) {

        $appCtx = ApplicationContext::getInstance();

        $namespace = (new \ReflectionClass($this))->getNamespaceName();
        $moduleName = $appCtx->getProduct() . substr($namespace, strrpos($namespace, '\\'));

        $action = new GenericAction($moduleName, 'API ' . $path,
            function (ActionContext $context) use ($auth, $appCtx) {

                $reqCtx = $context->getRequestContext();

                if (!$reqCtx->getAuth()->hasRights($auth)) {

                    throw $appCtx->getErrorManager()->getFormattedError(ERR_PERMISSION_DENIED);

                }

                return true;

            }, function (ActionContext $context) use ($requiredParameters, $appCtx) {

                $errorManager = $appCtx->getErrorManager();

                foreach ($requiredParameters as $requiredParameter) {

                    $explodedParameterPath = explode('.', $requiredParameter);
                    $params = $context->getParams();
                    $completePath = '';
                    foreach ($explodedParameterPath as $paramPath) {
                        $completePath .= $paramPath;
                        if (!isset($params[$paramPath])) {
                            $errorManager->addError(ERROR_MISSING_PARAM, $completePath);
                            break;
                        }
                        $params = $params[$paramPath];
                        $completePath .= '.';
                    }

                }

                if (!empty($errorManager->getErrors())) {
                    throw $errorManager->getFormattedError();
                }

            }, function (ActionContext $context) use ($moduleName, $actionName, $appCtx) {

                return $appCtx->getAction($moduleName, $actionName)->process($context);

            });


        ApplicationContext::getInstance()->addRoute($path, $action, $defaultFields);

    }

}