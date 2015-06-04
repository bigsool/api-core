<?php

namespace Core\Doctrine\Command;

use Core\Application;
use Core\Context\ApplicationContext;
use Core\Doctrine\Tools\EntityGenerator;
use Core\Module\AggregatedModuleEntity;
use Core\Module\MagicalModuleManager;
use Core\Module\ModelAspect;
use Core\Registry;
use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\Driver\YamlDriver;
use Doctrine\ORM\Tools\DisconnectedClassMetadataFactory;
use Doctrine\ORM\Tools\Setup;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Yaml;

class BuildEntitiesCommand extends Command {

    protected $modelDIr;

    /**
     * @var ProgressBar
     */
    protected $progress;

    /**
     * @var float
     */
    protected $prevProgression = 0;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    protected function configure () {

        $this
            ->setName('core:buildEntities')
            ->setDescription('Fetch and merge YML entities en generate PHP files')
            ->addArgument(
                'product',
                InputArgument::REQUIRED,
                'For which product do you want to generate entities ? Archipad ?'
            );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute (InputInterface $input, OutputInterface $output) {

        $this->input = $input;
        $this->output = $output;

        $this->setCurrentProgression(0);

        $product = $input->getArgument('product');
        $applicationClassName = $product . '\\Application';

        if (!class_exists($applicationClassName)) {
            throw new \RuntimeException("Class {$applicationClassName} not found");
        }

        $application = $applicationClassName::getInstance();
        if (!($application instanceof Application)) {
            throw new \RuntimeException("{$applicationClassName} is not an instance of \\Core\\Application");
        }

        $modulesManagers = $application->getModuleManagers();
        $refMethLoadModules = new \ReflectionMethod($application,'loadModules');
        $refMethLoadModules->setAccessible(true);
        $refMethLoadModules->invoke($application);

        $magicalModuleManagers = [];

        $this->setCurrentProgression(5);

        $appCtx = ApplicationContext::getInstance();

        // get and merge all yml
        $ymls = [];
        foreach ($modulesManagers as $moduleManager) {
            $loadYml = function (\ReflectionClass $class) use (&$ymls, &$loadYml) {

                if (($parentClass = $class->getParentClass()) && !$parentClass->isAbstract()) {
                    $loadYml($parentClass);
                }
                $moduleFolder = dirname($class->getFileName());
                $modelFiles = glob($moduleFolder . '/Model/*.dcm.yml');
                foreach ($modelFiles as $modelFile) {
                    $ymls = array_merge_recursive($ymls, Yaml::parse($modelFile));
                }

            };
            if ($moduleManager instanceof MagicalModuleManager) {
                $magicalModuleManagers[] = $moduleManager;
            }
            $loadYml(new \ReflectionClass($moduleManager));
        }

        array_walk_recursive($ymls, function (&$value, $key) use ($product) {

            if ($key == 'targetEntity') {
                $value = str_replace('Core\Model\\', "$product\\Model\\", $value);
            }
        });

        // write new yml files
        $rootDir = substr(__DIR__, 0, strrpos(__DIR__, DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR));
        if ($rootDir == '') {
            // SHOULD HAPPEN ONLY IF YOU ARE TESTING CORE
            $rootDir = __DIR__ . '/../../../../';
        }
        $this->modelDIr = $rootDir . '/model';
        if (!is_dir($this->modelDIr)) {
            mkdir($this->modelDIr);
        }

        $aggregatedModuleEntities = [];
        foreach ($appCtx->getModuleManagers() as $moduleManager) {
            foreach ($moduleManager->getModuleEntities() as $modulesEntity) {
                if ($modulesEntity instanceof AggregatedModuleEntity) {
                    $aggregatedModuleEntities[] = $modulesEntity;
                }
            }
        }

        $driver = new YamlDriver($this->modelDIr);
        $config = Setup::createXMLMetadataConfiguration(array($this->modelDIr));
        $config->setMetadataDriverImpl(
            $driver
        );

        $em = EntityManager::create($this->getHelper('db')->getConnection(), $config);
        $cmf = new DisconnectedClassMetadataFactory();
        $cmf->setEntityManager($em);

        $this->setCurrentProgression(20);

        $classNames = $this->generateYmlsAndEntities($ymls, $product, $cmf, $rootDir);

        $this->setCurrentProgression(60);

        $this->generateProxies($classNames, $em, $rootDir);

        $this->setCurrentProgression(90);

        $this->generateMagicalEntities($aggregatedModuleEntities, $rootDir);

        $this->finishProgress();

    }

    protected function setCurrentProgression ($current) {

        if ($this->input->getOption('verbose')) {
            return;
        }
        $current = round($current);
        if ($current > $this->prevProgression) {
            $this->getProgress()->advance($current - $this->prevProgression);
            $this->prevProgression = $current;
        }

    }

    /**
     *
     * @return ProgressBar
     */
    protected function getProgress () {

        if (!isset($this->progress)) {
            $this->progress = new ProgressBar($this->output, 100);
            $this->progress->start();
        }

        return $this->progress;
    }

    /**
     * @param array                            $ymls
     * @param string                           $product
     * @param DisconnectedClassMetadataFactory $cmf
     * @param string                           $rootDir
     *
     * @return array
     */
    protected function generateYmlsAndEntities (array $ymls, $product, DisconnectedClassMetadataFactory $cmf,
                                                $rootDir) {

        $classNames = [];

        foreach ($ymls as $modelName => $modelData) {
            $ymlContent = (new Dumper())->dump([$product . '\\Model\\' . $modelName => $modelData], 10);
            $ymlFilePath = $this->modelDIr . '/' . $product . '.Model.' . $modelName . '.dcm.yml';
            file_put_contents($ymlFilePath, $ymlContent);

            $className = $product . '\\Model\\' . $modelName;
            $metadata = $cmf->getMetadataFor($className);

            if (count($metadata->getIdentifier()) == 0) {
                unlink($ymlFilePath);
                continue;
            }
            $classNames[] = $className;

            $generator = new EntityGenerator();
            $generator->setFieldVisibility(EntityGenerator::FIELD_VISIBLE_PROTECTED);
            $generator->setBackupExisting(false);
            $generator->setGenerateAnnotations(true);
            $generator->setGenerateStubMethods(true);
            $generator->setRegenerateEntityIfExists(true);
            $generator->setUpdateEntityIfExists(true);
            //$generator->setAnnotationPrefix('');
            $generator->generate(array($metadata), $rootDir . '/src');
        }

        return $classNames;

    }

    /**
     * @param string[]      $classNames
     * @param EntityManager $em
     * @param string        $rootDir
     */
    protected function generateProxies (array $classNames, EntityManager $em, $rootDir) {

        foreach ($classNames as $_className) {
            // Proxy works only if all model are generated
            $em->getProxyFactory()
               ->generateProxyClasses(array($em->getClassMetadata($_className)), $rootDir . '/proxy');
        }
    }

    /**
     * @param AggregatedModuleEntity[] $aggregatedModuleEntities
     * @param string                   $rootDir
     */
    protected function generateMagicalEntities ($aggregatedModuleEntities, $rootDir) {

        foreach ($aggregatedModuleEntities as $aggregatedModuleEntity) {

            $modelAspects = $aggregatedModuleEntity->getDefinition()->getModelAspects();
            $mainEntity = $aggregatedModuleEntity->getDefinition()->getMainAspect();

            $magicalEntityName = $aggregatedModuleEntity->getDefinition()->getEntityName();
            $class = $this->createMagicalClassHeader($magicalEntityName);
            $class .= $this->createMagicalConstructor($mainEntity->getModel());
            //$class .= $this->createMagicalModuleManagerGetter($magicalEntityName);
            $class .= $this->createMagicalMainEntityMethods($mainEntity->getModel());

            foreach ($modelAspects as $modelAspect) {
                $class .= $this->generateMagicalSetterAndGetter($mainEntity->getModel(), $modelAspect);
            }

            $class .= $this->createMagicalClassFooter();

            $product = ApplicationContext::getInstance()->getProduct();
            file_put_contents($rootDir . '/src/' . $product . '/Model/' . $magicalEntityName . '.php', $class);

        }
    }

    protected function createMagicalModuleManagerGetter ($magicalEntityName) {

        return <<<MAGICAL_MODULE_MANAGER_GETTER
    /**
     * @return MagicalModuleManager
     */
    public function getMagicalModuleManager() {

        \$moduleManagers =  ApplicationContext::getInstance()->getModuleManagers();
        foreach (\$moduleManagers as \$moduleManager) {
            if (!(\$moduleManager instanceof MagicalModuleManager)) {
                continue;
            }
            \$classComponents = explode('\\\\', get_class(\$moduleManager));
            \$magicalEntityName = \$classComponents[count(\$classComponents) - 2];
            if (\$magicalEntityName == '$magicalEntityName') {
                return \$moduleManager;
            }
        }

        throw new \RuntimeException('MagicalModuleManager not found');

    }


MAGICAL_MODULE_MANAGER_GETTER;

    }

    protected function createMagicalClassHeader ($magicalEntityName) {

        $product = ApplicationContext::getInstance()->getProduct();

        return <<<CLASS_HEADER
<?php

namespace $product\Model;

use Core\Context\ApplicationContext;
use Core\Module\MagicalEntity;
use Core\Module\MagicalModuleManager;

class $magicalEntityName extends MagicalEntity {


CLASS_HEADER;

    }

    protected function createMagicalConstructor ($modelName) {

        $varName = lcfirst($modelName);

        return <<<CONSTRUCTOR
    /**
     * @var $modelName
     */
    protected \$$varName;

    /**
     * @param $modelName \$$varName
     */
    public function __construct ($modelName \$$varName) {

        \$this->$varName = \$$varName;

    }


CONSTRUCTOR;

    }

    protected function createMagicalMainEntityMethods ($modelName) {

        $varName = lcfirst($modelName);

        return <<<CONSTRUCTOR
    /**
     * @return $modelName
     */
    public function get$modelName () {

        return \$this->getMainEntity();

    }

    /**
     * @return $modelName
     */
    public function getMainEntity () {

        return \$this->$varName;

    }


CONSTRUCTOR;

    }

    protected function generateMagicalSetterAndGetter ($mainModelName, ModelAspect $modelAspect) {

        $modelName = $modelAspect->getModel();
        $varName = lcfirst($modelName);

        $getterUnrestrictedChain = $getterChain = $getterChainUntilEntity = "\$this->get$mainModelName()";
        foreach (explode('.', $modelAspect->getRelativeField()) as $fieldName) {
            $getterChainUntilEntity = $getterUnrestrictedChain;
            $getterChain .= '->get' . ucfirst($fieldName) . '()';
            $getterUnrestrictedChain .= '->getUnrestricted' . ucfirst($fieldName) . '()';
        }

        $mapping = $this->getPreviousMapping($modelAspect, $mainModelName);

        $field1 = $mapping['fieldName'];
        $field2 = isset($mapping['mappedBy']) ? $mapping['mappedBy'] : $mapping['inversedBy'];

        $prefix1 = 'set';
        $prefix2 = 'set';

        if ($mapping['type'] == ClassMetadataInfo::ONE_TO_MANY || $mapping['type'] == ClassMetadataInfo::MANY_TO_MANY) {
            $field1 = Inflector::singularize($field1);
            $prefix1 = "add";
        }

        if ($mapping['type'] == ClassMetadataInfo::MANY_TO_ONE || $mapping['type'] == ClassMetadataInfo::MANY_TO_MANY) {
            $field2 = Inflector::singularize($mapping['inversedBy']);
            $prefix2 = "add";
        }

        $setterFromMainEntity = $prefix1 . ucfirst($field1);
        $setterFromModelAspect = $prefix2 . ucfirst($field2);

        $getterMethodName = 'get';
        $getterUnrestrictedMethodName = 'getUnrestricted';
        $setterMethodName = $prefix1;
        $prefixToConcat = '';
        foreach (explode('.', $modelAspect->getPrefix()) as $prefix) {
            $getterMethodName .= ucfirst($prefix);
            $getterUnrestrictedMethodName .= ucfirst($prefix);
            $setterMethodName .= ucfirst($prefixToConcat);
            $prefixToConcat = $prefix;
        }
        $setterMethodName .= ucfirst(Inflector::singularize($prefixToConcat));

        return <<<GETTER_AND_SETTER
    /**
     * @return $modelName
     */
    public function $getterMethodName () {

        return $getterChain;

    }

    /**
     * @return $modelName
     */
    public function $getterUnrestrictedMethodName () {

        return $getterUnrestrictedChain;

    }

    /**
     * @param $modelName \$$varName
     */
    public function $setterMethodName ($modelName \$$varName) {

        {$getterChainUntilEntity}->$setterFromMainEntity(\$$varName);
        \${$varName}->$setterFromModelAspect($getterChainUntilEntity);

    }


GETTER_AND_SETTER;

    }

    /**
     * @param ModelAspect $modelAspect
     * @param string      $mainModelName
     *
     * @return array
     * @throws \Doctrine\ORM\Mapping\MappingException
     */
    protected function getPreviousMapping (ModelAspect $modelAspect, $mainModelName) {

        $appCtx = ApplicationContext::getInstance();

        $entityClassName = Registry::realModelClassName($mainModelName);
        $metadata = $appCtx->getClassMetadata($entityClassName);
        $mapping = NULL;

        foreach (explode('.', $modelAspect->getRelativeField()) as $fieldName) {
            $mapping = $metadata->getAssociationMapping($fieldName);
            $metadata = $appCtx->getClassMetadata($mapping['targetEntity']);
        }

        return $mapping;

    }

    protected function createMagicalClassFooter () {

        return <<<CLASS_FOOTER
}
CLASS_FOOTER;

    }

    protected function finishProgress () {

        if ($this->input->getOption('verbose')) {
            return;
        }
        $this->getProgress()->finish();

    }
}