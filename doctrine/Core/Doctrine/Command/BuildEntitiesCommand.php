<?php

namespace Core\Doctrine\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class BuildEntitiesCommand extends Command
{
	
	protected $tmpDir;
	protected $progress;
	protected $prevProgression = 0;
	protected $input;
	protected $output;
	
    protected function configure()
    {
        $this
            ->setName('core:buildEntities')
            ->setDescription('Fetch and merge XML entities en generate PHP files')
            ->addArgument(
                'product',
                InputArgument::REQUIRED,
                'For which product do you want to generate entities ?'
            )
        ;
    }
	
	/**
	 * 
	 * @return \Symfony\Component\Console\Helper\ProgressHelper
	 */
	protected function getProgress () {
		if (!isset($this->progress)) {
			$this->progress = $this->getHelper('progress');
			$this->progress->start($this->output, 100);
		}
		return $this->progress;
	}
	
	protected function setCurrentProgression($current) {
		
		if ($this->input->getOption('verbose')) {
			return;
		}
		$current = round($current);
		if ($current > $this->prevProgression) {
			$this->getProgress()->advance($current-$this->prevProgression);
			$this->prevProgression = $current;
		}
		
	}
	
	protected function finishProgress () {
		if ($this->input->getOption('verbose')) {
			return;
		}
		$this->getProgress()->finish();
	}

	protected function execute(InputInterface $input, OutputInterface $output)
    {
		
		$this->input = $input;
		$this->output = $output;
		
        $finalProduct = $input->getArgument('product');
		$root = realpath(__DIR__.'/../../../../');
		$finalProductPath = $root.'/'.$finalProduct;
		
		if (!is_dir($finalProductPath)) {
			throw new \InvalidArgumentException(
                sprintf("The product is invalide because the path '<info>%s</info>' is not a directory.", $finalProductPath)
            );
		}
		
		
		$this->setCurrentProgression(0);
		
		$isCore = $finalProduct == 'Core';
		
		$products = array('Core');
		if (!$isCore) {
			$dirs = array_map('basename',glob($root.'/*',GLOB_ONLYDIR));
			foreach ($dirs as $dir) {
				if ($dir == 'www' || $dir == 'Core' || $dir == $finalProduct) continue;
				$products[] = $dir;
			}
			$products[] = $finalProduct;
		}
		
		$this->setCurrentProgression(2);
		
		if (!$isCore) {

			$configModuleFile = $root.'/'.$finalProduct.'/Config/product.php';
			if (!file_exists($configModuleFile)) {
				throw new \InvalidArgumentException(
					sprintf("The file which contains the module list cannot be found at '<info>%s</info>'.", $configModuleFile)
				);
			}

			$config = require $configModuleFile;
			if (!isset($config['modules'])) {
				throw new \InvalidArgumentException(
					sprintf("The module list cannot be found in the file '<info>%s</info>'.", $configModuleFile)
				);
			}
			$modules = $config['modules'];
		
		}
		else {
		
			$modules = array_map('basename',glob($root.'/Core/Service/*',GLOB_ONLYDIR));
		
		}
		
		$this->setCurrentProgression(5);
		
		// get all xmls by module and model
		$xmls = array();
		$i = 0;
		foreach ($products as $product) {
			
			$basePath = $root.'/'.$product.'/Service/';
			$newXmls = $this->fetchXmls($input, $output, $basePath, $modules, !$isCore, $i, count($products));
			$xmls = array_merge_recursive($xmls, $newXmls);
			
			$this->setCurrentProgression(5 + (++$i / count($products) * 10));
			
		}
		
		$input->getOption('verbose') && $output->writeln('');
		
		// init metadata factory

		$this->tmpDir = __DIR__.'/'.uniqid('',true);
		mkdir($this->tmpDir);
		
		try {
		
			$driver = new \Doctrine\ORM\Mapping\Driver\XmlDriver($this->tmpDir);
			$config = Setup::createXMLMetadataConfiguration(array($this->tmpDir));
			$config->setMetadataDriverImpl(
			   $driver
			);

			$em = EntityManager::create($this->getHelper('db')->getConnection(), $config);
			$cmf = new \Doctrine\ORM\Tools\DisconnectedClassMetadataFactory();
			$cmf->setEntityManager($em);
			
			$this->setCurrentProgression(20);

			// merge xmls and generate entities
			$models = array();
			$prevProgress = 0;
			$i = 0;
			foreach ($xmls as $modelName => $modules) {
				$models[$modelName] = $this->generateModel($input, $output, $modelName, $modules, $root, $finalProduct, $cmf, $em);
				$this->setCurrentProgression(20 + (++$i / count($xmls) * 40));
			}
			foreach ($models as $modelName => $moduleName) {
				$this->generateProxy($input, $output, $modelName, $moduleName, $root, $finalProduct, $em);
				$this->setCurrentProgression(20 + (++$i / count($xmls) * 40));
			}
			
			$this->finishProgress();

			exec('rm -fr '.escapeshellarg($this->tmpDir));
		
		}
		catch(\Exception $e) {
			
			if (is_dir($this->tmpDir)) {
				exec('rm -fr '.escapeshellarg($this->tmpDir));
			}
			
			throw $e;
			
		}
		
    }
	
	protected function fetchXmls (InputInterface $input, OutputInterface $output, $basePath, array $modules, $addExtention, $currProductNb, $nbProduct) {
		
		$xmls = array();
		
		$j = 0;
		foreach ($modules as $module) {

			$path = $basePath.$module.'/Model/XML';
			if (!is_dir($path)) continue;
			$xmlFiles = array_map('basename',glob($path.'/*.dcm.xml'));
			foreach ($xmlFiles as $xmlFile) {

				$modelName = substr($xmlFile,0,-8);
				if (!isset($xmls[$modelName])) $xmls[$modelName] = array();
				
				$dom = new \DOMDocument;
				if (!$dom->load(realpath($path.'/'.$xmlFile))) {
					throw new \InvalidArgumentException(
						sprintf("The file '<info>%s</info>' seems to be invalide.", $xmlFile)
					);
				}
				$input->getOption('verbose') && $output->writeln(sprintf("The file '<info>%s</info>' is loaded.", realpath($path.'/'.$xmlFile)));
				
				$isExtenstion = $dom->documentElement->tagName == 'extention';
				if (!$addExtention && $isExtenstion) {
					$input->getOption('verbose') && $output->writeln(sprintf("The file '<info>%s</info>' is ignored.", realpath($path.'/'.$xmlFile)));
					continue;
				}
				
				$addFn = $isExtenstion ? 'array_push' : 'array_unshift';
				if (!isset($xmls[$modelName][$module])) {
					if ($isExtenstion) {
						$xmls[$modelName][$module] = array();
					}
					else {
						$xmls[$modelName] = array_merge(array($module=>array()), $xmls[$modelName]);
					}
				}
				$addFn($xmls[$modelName][$module], $dom);
		
			}
			
			$this->setCurrentProgression(5 + ($currProductNb / $nbProduct * 10) + (1 * (++$j / count($modules)) / $nbProduct * 10));
			
		}
		return $xmls;
		
	}
	
	protected function generateProxy (InputInterface $input, OutputInterface $output, $modelName, $moduleName, $root, $finalProduct, EntityManager $em) {
				
		$className = 'Model\\'.$modelName;
		$proxyDir = $root.'/'.$finalProduct.'/Proxy/';
		$proxyPath = $proxyDir.'__CG__'.str_replace('\\', '', $className).'.php';
		$em->getProxyFactory()->generateProxyClasses(array($em->getClassMetadata($className)), $proxyDir);
		
		if (!file_exists($proxyPath)) {
			throw new \InvalidArgumentException(
				sprintf("The proxy '<info>%s</info>' seems to not be created because the file '<info>%s</info>' was not found.", $modelName, $proxyPath)
			);
		}
		
		$input->getOption('verbose') && $output->writeln(sprintf("The proxy '<info>%s</info>' was generated.", $modelName));
				
	}
	
	protected function generateModel (InputInterface $input, OutputInterface $output, $modelName, array $modules, $root, $finalProduct, \Doctrine\ORM\Tools\DisconnectedClassMetadataFactory $cmf, EntityManager $em) {
		
		$moduleName = '';
		$mergedXml = NULL;
		
		//$namespace = "{$finalProduct}\\Service\\";
		$namespace = '';

		$input->getOption('verbose') && $output->write(sprintf("Merging of the model '<info>%s</info>' ...",$modelName));

		foreach ($modules as $module => $xmlArray) {

			foreach ($xmlArray as $xml) {

				if ($xml->documentElement->tagName != 'extention') {
					if (!$moduleName) {
						$moduleName = $module;
						//$namespace .= "$moduleName\\Model\\";
						$namespace .= 'Model\\';
					}
					else if ($moduleName != $module) {
						throw new \InvalidArgumentException(
							sprintf("The moduleName '<info>%s</info>' is different from the module '<info>%s</info>'.", $moduleName, $module)
						);
					}
				}
				if (!$mergedXml) {
					$mergedXml = $xml;
					$elements = $xml->getElementsByTagName('entity');
					$entity = $elements->item(0);
					$entity->setAttribute('name', $namespace.$modelName);
				} else {
					$mergedElements = $mergedXml->getElementsByTagName('entity');
					$mergedEntity = $mergedElements->item(0);
					$elements = $xml->getElementsByTagName('entity');
					$entity = $elements->item(0);
					$properties = $entity->childNodes;
					foreach ($properties as $property) {
						$mergedEntity->appendChild($mergedXml->importNode($property, true));
					}

				}

				$input->getOption('verbose') && $output->write('.');

			}

		}

		$input->getOption('verbose') && $output->writeln(' <info>OK</info>');
		$modelFilename = str_replace('\\', '.', $namespace).$modelName.'.dcm.xml';
		$tmpFile = $this->tmpDir.'/'.$modelFilename;

		$input->getOption('verbose') && $output->write(sprintf("Creation of the file '<info>%s</info>' ... ",$modelFilename));

		$xmlContent = $mergedXml->saveXML();
		if (!$xmlContent) {
			throw new \InvalidArgumentException(
				sprintf("The xml for the file '<info>%s</info>' cannot be generated.", $tmpFile)
			);
		}

		if (!file_put_contents($tmpFile, $xmlContent)) {
			throw new \InvalidArgumentException(
				sprintf("The file '<info>%s</info>' cannot be created.", $tmpFile)
			);
		}

		$input->getOption('verbose') && $output->writeln('<info>DONE</info>');

		$rootModel =  $root.'/'.$finalProduct.'/Service/'.$moduleName;
		$modelDir = $rootModel.'/Model/';
		$modelPath = $modelDir.$modelName.'.php';
		$metadata = $cmf->getMetadataFor($namespace.$modelName);
		$generator = new \Doctrine\ORM\Tools\EntityGenerator();
		$generator->setBackupExisting(false);
		$generator->setGenerateAnnotations(true);
		$generator->setGenerateStubMethods(true);
		$generator->setRegenerateEntityIfExists(true);
		$generator->setUpdateEntityIfExists(true);
		//$generator->setAnnotationPrefix('');
		$generator->generate(array($metadata), $rootModel);

		if (!file_exists($modelPath)) {
			throw new \InvalidArgumentException(
				sprintf("The model '<info>%s</info>' seems to not be created because the file '<info>%s</info>' was not found.", $modelName, $modelPath)
			);
		}

		$input->getOption('verbose') && $output->writeln(sprintf("The model '<info>%s</info>' was generated.", $modelName));

		$input->getOption('verbose') && $output->writeln('');
		
		return $moduleName;
		
	}
}