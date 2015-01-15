<?php

namespace Core\Doctrine\Command;

use Core\Application;
use Doctrine\ORM\EntityManager;
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

    protected function finishProgress () {

        if ($this->input->getOption('verbose')) {
            return;
        }
        $this->getProgress()->finish();
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

        $application = new $applicationClassName;
        if (!($application instanceof Application)) {
            throw new \RuntimeException("{$applicationClassName} is not an instance of \\Core\\Application");
        }

        $modulesManagers = $application->getModuleManagers();

        $this->setCurrentProgression(5);

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
            $loadYml(new \ReflectionClass($moduleManager));
        }

        // write new yml files
        $rootDir = substr(__DIR__, 0, strrpos(__DIR__, DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR));
        if ($rootDir == '') {
            // SHOULD HAPEN ONLY IF YOU ARE TESTING CORE
            $rootDir = __DIR__.'/../../../../';
        }
        $this->modelDIr = $rootDir . '/model';
        if (!is_dir($this->modelDIr)) {
            mkdir($this->modelDIr);
        }

        $driver = new \Doctrine\ORM\Mapping\Driver\YamlDriver($this->modelDIr);
        $config = Setup::createXMLMetadataConfiguration(array($this->modelDIr));
        $config->setMetadataDriverImpl(
            $driver
        );

        $em = EntityManager::create($this->getHelper('db')->getConnection(), $config);
        $cmf = new \Doctrine\ORM\Tools\DisconnectedClassMetadataFactory();
        $cmf->setEntityManager($em);

        $this->setCurrentProgression(20);

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
            $generator = new \Doctrine\ORM\Tools\EntityGenerator();
            $generator->setBackupExisting(false);
            $generator->setGenerateAnnotations(true);
            $generator->setGenerateStubMethods(true);
            $generator->setRegenerateEntityIfExists(true);
            $generator->setUpdateEntityIfExists(true);
            //$generator->setAnnotationPrefix('');
            $generator->generate(array($metadata), $rootDir . '/src');

            // Proxy works only if all model are generated
            //$em->getProxyFactory()->generateProxyClasses(array($em->getClassMetadata($className)), $rootDir . '/proxy');
        }

        $this->finishProgress();

    }
}