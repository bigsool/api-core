<?php


namespace Archiweb;


use Archiweb\Config\ConfigManager;
use Archiweb\Context\ActionContext;
use Archiweb\Context\ApplicationContext;
use Archiweb\Context\FindQueryContext;
use Archiweb\Context\RequestContext;
use Archiweb\Error\FormattedError;
use Archiweb\Field\KeyPath as FieldKeyPath;
use Archiweb\Field\KeyPath;
use Archiweb\Filter\StringFilter;
use Archiweb\Module\ModuleManager;
use Archiweb\RPC\Handler;
use Archiweb\RPC\JSON;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext as SymfonyRequestContext;

define('ROOT_DIR', __DIR__ . '/../..');

class Application {

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var ApplicationContext
     */
    protected $appCtx;

    public function __construct () {

        $this->appCtx = $this->createApplicationContext();

    }

    /**
     * @return ApplicationContext
     */
    protected function createApplicationContext () {

        $this->appCtx = ApplicationContext::getInstance();

        require ROOT_DIR . '/doctrine/config.php';
        require_once ROOT_DIR . '/config/errors.php';
        loadErrors($this->appCtx->getErrorManager());


        /**
         * @var EntityManager $entityManager ;
         */
        $this->appCtx->setEntityManager($this->entityManager = $entityManager);
        $this->appCtx->setRuleProcessor(new RuleProcessor());

        $entityManager->beginTransaction();

        return $this->appCtx;

    }

    /**
     *
     */
    public function run () {

        try {

            // load config
            $configFiles = [ROOT_DIR . '/config/default.yml'];
            $routesFile = ROOT_DIR . '/config/routes.yml';

            new ConfigManager($configFiles, $routesFile);

            $modules = array_map('basename', glob(__DIR__ . '/Module/*', GLOB_ONLYDIR));
            foreach ($modules as $moduleName) {
                $className = "\\Archiweb\\Module\\$moduleName\\ModuleManager";
                /**
                 * @var ModuleManager $moduleManager
                 */
                $moduleManager = new $className;
                $moduleManager->load($this->appCtx);
            }

            // default RPCHandler
            $rpcHandler = new JSON();

            try {
                $request = Request::createFromGlobals();
                $sfReqCtx = new SymfonyRequestContext();
                $sfReqCtx->fromRequest($request);

                $protocol = strstr(trim($request->getPathInfo(), '/'), '/', true);
                $rpcClassName = '\Archiweb\RPC\\' . $protocol;
                if (!$protocol || !class_exists($rpcClassName)) {
                    throw $this->appCtx->getErrorManager()->getFormattedError(ERR_PROTOCOL_IS_INVALID);
                }

                $rpcHandler = new $rpcClassName();
                /**
                 * @var Handler $rpcHandler
                 */

                $rpcHandler->parse($request);

                $matcher = new UrlMatcher($this->appCtx->getRoutes(), $sfReqCtx);

                $reqCtx = new RequestContext();
                $reqCtx->setParams($rpcHandler->getParams());
                $reqCtx->setReturnedRootEntity($rpcHandler->getReturnedRootEntity());
                $reqCtx->setReturnedKeyPaths(array_map(function ($field) {

                    return new KeyPath($field);

                }, $rpcHandler->getReturnedFields()));

                /**
                 * @var Controller $controller
                 */
                try {
                    $controller = $matcher->match($rpcHandler->getPath())['controller'];
                }
                catch (\Exception $e) {
                    throw $this->appCtx->getErrorManager()->getFormattedError(ERR_METHOD_NOT_FOUND);
                }
                $actCtx = new ActionContext($reqCtx);

                $result = $controller->apply($actCtx);

                $serializer = new Serializer($reqCtx);

                $response = $rpcHandler->getSuccessResponse($serializer, $result);

                $this->entityManager->commit();

            }
            catch (FormattedError $e) {
                $response = $rpcHandler->getErrorResponse($e);
            }
            catch (\Exception $e) {
                $response = $rpcHandler->getErrorResponse(new FormattedError(['code'    => $e->getCode(),
                                                                              'message' => $e->getMessage()
                                                                             ]));
            }

            $response->send();

        }
        catch (\Exception $e) {

            exit('fatal error code ' . $e->getCode() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());

        }

    }

    protected function getAuth ($name) {

        $findCtx = new FindQueryContext('User');
        $findCtx->addFilter(new StringFilter('User', '', 'name = "' . $name . '"'));
        $findCtx->addKeyPath(new FieldKeyPath('*'));
        $user = $this->appCtx->getNewRegistry()->find($findCtx, false);

        return $user;

    }

} 