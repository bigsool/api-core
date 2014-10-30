<?php


namespace Archiweb;


use Archiweb\Context\ActionContext;
use Archiweb\Context\ApplicationContext;
use Archiweb\Context\FindQueryContext;
use Archiweb\Context\RequestContext;
use Archiweb\Error\FormattedError;
use Archiweb\Filter\StringFilter;
use Archiweb\Module\ModuleManager;
use Archiweb\RPC\JSONP;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext as SymfonyRequestContext;
use \Archiweb\Config\ConfigManager;
use Archiweb\Field\KeyPath as FieldKeyPath;
use Archiweb\DoctrineProxyHandler;

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

    protected function getAuth ($name) {

        $findCtx = new FindQueryContext( $this->appCtx, 'User');
        $findCtx->addFilter(new StringFilter('User', '', 'name = "' . $name . '"', 'SELECT'));
        $findCtx->addKeyPath(new FieldKeyPath('*'));
        $user = $this->appCtx->getNewRegistry()->find($findCtx, false);

        return $user;

    }

    /**
     * @return ApplicationContext
     */
    protected function createApplicationContext () {

        $this->appCtx = ApplicationContext::getInstance();

        require __DIR__ . '/../../doctrine/config.php';
        require_once __DIR__ . '/../../config/errors.php';
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
            $configFiles = [__DIR__.'/Config/default.yml',__DIR__.'/Config/default.yml'];
            $routesFile = __DIR__.'/Config/routes.yml';
            $configManager = new ConfigManager($configFiles, $routesFile);

            $user = $this->getAuth('thierry');

            $modules = array_map('basename', glob(__DIR__ . '/Module/*', GLOB_ONLYDIR));
            foreach ($modules as $moduleName) {
                $className = "\\Archiweb\\Module\\$moduleName\\ModuleManager";
                /**
                 * @var ModuleManager $moduleManager
                 */
                $moduleManager = new $className;
                $moduleManager->load($this->appCtx);
            }

            try {
                $request = Request::createFromGlobals();
                $sfReqCtx = new SymfonyRequestContext();
                $sfReqCtx->fromRequest($request);
                $rpcHandler = new JSONP($this->appCtx, $request);
                $matcher = new UrlMatcher($this->appCtx->getRoutes(), $sfReqCtx);
                $reqCtx = new RequestContext($this->appCtx);
                $reqCtx->setParams($rpcHandler->getParams());
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

                $actCtx['authUser'] = $user[0];

                $result = $controller->apply($actCtx);

                $requiredFields = ['User' => ['email','password','ownedCompany'], 'Company' => ['name']];

                $serializer = new Serializer($requiredFields);

                $response = new Response($serializer->serialize($result,'json'));

                var_dump($response);

                $this->entityManager->commit();

            }
            catch (FormattedError $e) {
                (new Response((string)$e))->send();
            }
            catch (\Exception $e) {
                (new Response(json_encode(['code' => $e->getCode(), 'message' => $e->getMessage()])))->send();
            }

        }
        catch (\Exception $e) {

            exit('fatal error code '.$e->getCode().' '.$e->getMessage().' '.$e->getTraceAsString());

        }

    }

} 