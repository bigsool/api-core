<?php

namespace Core\Deploy\Command;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Yaml\Yaml;

class Install extends Base {

    protected $dbConfig = array(
        'current' => array(),
        'next'    => array(),
    );

    protected $envConf;

    protected $dbConfigLinkName;

    protected $dbConfigLinkNameArchiweb;

    protected $dbConfigLinkArchiweb;

    protected $dbConfigRealPath;

    protected $nextDBConfigRealPath;

    protected $dbConfigRealPathArchiweb;

    protected $nextDBConfigRealPathArchiweb;

    protected $setDownPath;

    protected $isDownPath;

    protected $setDownPathArchiweb;

    protected $isDownPathArchiweb;

    protected $deployDestDir;

    protected $dbConfigDirectory;

    protected $dbConfigFilenames = array(
        'extra.1.yml',
        'extra.2.yml',
    );

    protected $dbConfigFilenamesArchiweb = array(
        'config_1.php',
        'config_2.php',
    );

    protected $dumpFolder;

    protected $isDown = false;

    /**
     * @var string
     */
    protected $configDir;

    protected $shouldRestoreArchiwebConfigLink = false;

    protected function configure () {

        parent::configure();

        $this
            ->setName('install')
            ->setDescription('Installing Command');

    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute (InputInterface $input, OutputInterface $output) {

        $output->writeln('');

        parent::execute($input, $output);

        if (!$this->confirm(sprintf("About to install <env>%s</env>, OK ?\n[Y/n] ", $this->env))) {
            $this->abort('Install aborted by user');
        }

        $this->getOutput()->writeln('');

        $this->generateOptimizedAutoLoader();

        $isFirstInstall = true;
        $isStageOrProd = $this->isStageOrProd();

        if ($isStageOrProd) {

            if (($isFirstInstall = $this->isFirstInstall())) {
                $this->checkIfConfigFilesExists();
            }

            $this->loadDBConfigs();

            if (!$isFirstInstall) {
                $this->confirmNextDBName();
            }

            $this->createConfigLink();

        }

        $this->runSanityChecks();

        try {
            if ($isStageOrProd) {

                if (!$isFirstInstall) {
                    $this->setDown();
                    $this->waitForTransactionsFinish();
                }

                foreach (['db', 'patchDb'] as $db) {

                    if (!array_key_exists($db, $this->dbConfig['current'])) {
                        continue;
                    }

                    $dumpPath = $this->dumpProdDB($db);
                    $this->clearNextDB($db);
                    $this->copyDataToNextDB($dumpPath, $db);

                }

            }

            $this->runUpgradeScripts();

            $this->changeTheLink();

            $this->shouldRestoreArchiwebConfigLink = false;

            $this->setUp();

        }
        catch (\Exception $e) {

            // If we set down the environment, we have to set it up
            if ($this->isDown) {
                $this->setUp();
            }
            throw $e;
        }

    }

    /**
     *
     * @param string $env
     *
     * @throws \RunTimeException
     */
    protected function setEnv ($env) {

        parent::setEnv($env);
        $this->configDir = $this->paths['root'] . '/config/' . $this->getEnv();
        $this->dumpFolder = $this->paths['root'] . '/dump';

        $this->getEnvConf();
        $configFolderArchiweb =
            $this->envConf['archiweb_path_root'] . $this->getEnv() . '-api/include/config/';
        $configDirArchiweb = $configFolderArchiweb . 'envs/';

        $this->dbConfigDirectory = $this->envConf['conf_directory'];

        $this->deployDestDir = Helper::getRemoteDestLink($this->paths['environmentFile']);

        $downFolder = $this->deployDestDir . '/vendor/api/core/config/';

        $this->setDownPath = $downFolder . 'setDown.yml';
        if (!file_exists($this->setDownPath)) {
            $downFolder = $this->deployDestDir . '/config/';
            $this->setDownPath = $downFolder . 'setDown.yml';
        }
        $this->isDownPath = $downFolder . 'isDown.yml';

        $this->setDownPathArchiweb = $configFolderArchiweb . 'config.setDown.php';
        $this->isDownPathArchiweb = $configFolderArchiweb . 'config.isDown.php';

        $this->dbConfigLinkName = $this->configDir . '/extra.yml';

        $this->dbConfigLinkNameArchiweb = $configDirArchiweb . 'config.php';

    }

    protected function isFirstInstall ($verbose = false) {

        if (!file_exists(Helper::getRemoteDestLink($this->paths['environmentFile']))) {
            if (!$verbose) {
                return true;
            }
            $this->getOutput()
                 ->writeln("<warning>No revision found on server.</warning>");
            if (!$this->confirm("Is It your first commit ?\n[Y/n] ")) {
                $this->abort('Installation aborted by user');
            }
        }

        return false;

    }

    protected function checkIfConfigFilesExists () {

        $this->getOutput()->write("Checking if config files exist ... ");

        foreach ($this->dbConfigFilenames as $filename) {

            $configPath = $this->dbConfigDirectory . '/' . $filename;
            if (!file_exists($configPath)) {
                $this->abort(sprintf('Config file %s cannot be found. You are supposed to create it.', $configPath));
            }

        }

        $this->getOutput()->writeln("OK\n");

    }

    protected function loadDBConfigs () {

        $this->getOutput()->write("Loading current config ... ");

        $isFirstInstall = $this->isFirstInstall();

        if (!$isFirstInstall) {

            $this->dbConfigRealPath =
                Helper::getRemoteDestLink($this->paths['environmentFile']) . '/config/' . $this->getEnv()
                . '/extra.yml';
            $this->dbConfig['current'] = $this->loadDBConfig($this->dbConfigRealPath);

            $this->getOutput()->writeln("OK");

        }
        else {

            $this->dbConfigRealPath = $this->dbConfigDirectory . '/' . $this->dbConfigFilenames[0];
            $this->dbConfig['current'] = $this->loadDBConfig($this->dbConfigRealPath);

            $this->getOutput()->writeln("OK");

        }

        // rotating config file for stage and prod
        if ($this->isStageOrProd()) {

            $this->getOutput()->write("Loading next config ... ");

            if ($isFirstInstall) {

                $this->nextDBConfigRealPath =
                    $this->dbConfigDirectory . '/' . $this->dbConfigFilenames[1 % count($this->dbConfigFilenames)];
                $this->dbConfig['next'] = $this->loadDBConfig($this->nextDBConfigRealPath);

                $this->nextDBConfigRealPathArchiweb = $this->dbConfigRealPathArchiweb =
                    $this->dbConfigDirectory . '/' . $this->dbConfigFilenamesArchiweb[1
                                                                                      % count($this->dbConfigFilenamesArchiweb)];

            }
            else {

                if (!is_link($this->dbConfigRealPath)) {
                    $this->abort(sprintf('The config file %s must be a link, but it is not', $this->dbConfigRealPath));
                }

                $dbConfigPath = realpath($this->dbConfigRealPath);
                $configFilename = basename($dbConfigPath);
                $configDirectory = dirname($dbConfigPath);

                if (($index = array_search($configFilename, $this->dbConfigFilenames)) === false) {
                    $this->abort(sprintf('The config filename %s is not expected', $configFilename));
                }

                $this->nextDBConfigRealPath =
                    $configDirectory . '/' . $this->dbConfigFilenames[($index + 1) % count($this->dbConfigFilenames)];

                $this->dbConfigRealPathArchiweb = $configDirectory . '/' . $this->dbConfigFilenamesArchiweb[$index];

                $this->nextDBConfigRealPathArchiweb =
                    $configDirectory . '/' . $this->dbConfigFilenamesArchiweb[($index + 1)
                                                                              % count($this->dbConfigFilenames)];

                $this->dbConfig['next'] = $this->loadDBConfig($this->nextDBConfigRealPath);

            }

            $this->getOutput()->writeln("OK");

        }

        $this->getOutput()->writeln('');

    }

    protected function loadDBConfig ($file) {

        if (!file_exists($file)) {
            $this->abort(sprintf('Config file %s cannot be found.', $file));
        }

        $config = Yaml::parse(file_get_contents($file));

        if (!isset($config) || !isset($config['db'])) {
            $this->abort(sprintf('Config cannot be found in the file %s', $file));
        }

        return $config;

    }

    protected function confirmNextDBName () {

        $output = $this->getOutput();

        foreach (['db', 'patchDb'] as $db) {

            if (!array_key_exists($db, $this->dbConfig['current'])) {
                continue;
            }

            $currentDBName = $this->dbConfig['current'][$db]['dbname'];
            $currentDBHost = $this->dbConfig['current'][$db]['host'];
            $nextDBName = $this->dbConfig['next'][$db]['dbname'];
            $nextDBHost = $this->dbConfig['next'][$db]['host'];

            $output->writeln(sprintf(
                                 'Current <env>%s</env> %s is <info>%s@%s</info>',
                                 $this->env, $db, $currentDBName, $currentDBHost
                             ));
            $output->writeln(sprintf(
                                 'Next <env>%s</env> %s will be <info>%s@%s</info>',
                                 $this->env, $db, $nextDBName, $nextDBHost
                             ));
            $output->writeln(sprintf(
                                 "<warning>Next <env>%s</env> %s will be TOTALLY ERASED !</warning>\n",
                                 $this->env, $db
                             ));

            $answer = $this->getQuestion()->ask(
                $this->getInput(),
                $output,
                new Question(sprintf("Are you sure you want to continue ? "
                                     . "All current data on <info>%s@%s</info> will be lost !"
                                     . "\n[next <env>%s</env> database name] ",
                                     $nextDBName, $nextDBHost, $this->env))
            );
            if ($answer != $nextDBName) {
                $this->abort(
                    sprintf('Wrong database name : %s - expected : %s', $answer, $nextDBName)
                );
            }

            $this->getOutput()->writeln('');

        }

    }

    protected function createConfigLink () {


        $this->getOutput()->write(sprintf('Creating config link to <info>%s</info> with the name <info>%s</info> ... ',
                                          $this->nextDBConfigRealPath, $this->dbConfigLinkName));

        if (file_exists($this->dbConfigLinkName) && !unlink($this->dbConfigLinkName)) {
            $this->abort(sprintf('Unable to remove existing config link <info>%s</info>', $this->dbConfigLinkName));
        }

        if (!symlink($this->nextDBConfigRealPath, $this->dbConfigLinkName)) {
            $this->abort('Unable to create config symlink, aborting...');
        }

        $this->getOutput()->writeln('OK');

        $this->getOutput()->write(sprintf('Creating config link to <info>%s</info> with the name <info>%s</info> ... ',
                                          $this->nextDBConfigRealPathArchiweb, $this->dbConfigLinkNameArchiweb));


        if (file_exists($this->dbConfigLinkNameArchiweb)) {

            if (file_exists($this->dbConfigLinkNameArchiweb) && !unlink($this->dbConfigLinkNameArchiweb)) {
                $this->abort(sprintf('Unable to remove existing config link <info>%s</info>',
                                     $this->dbConfigLinkNameArchiweb));
            }

            if (!symlink($this->nextDBConfigRealPathArchiweb, $this->dbConfigLinkNameArchiweb)) {
                $this->abort('Unable to create config symlink, aborting...');
            }

            $this->shouldRestoreArchiwebConfigLink = true;

            $this->getOutput()->writeln('OK');

        }
        else {
            $this->getOutput()->writeln('Cancelled');
        }

    }

    protected function runSanityChecks () {

        clearstatcache();

        // folder www
        $this->getOutput()->write('Checking the folder <info>www</info> ... ');
        $wwwPath = $this->paths['root'] . '/www';
        if (!file_exists($wwwPath)) {
            $this->abort('www folder does not exist');
        }
        if ((fileperms($wwwPath) & 0x0004) != 0x0004) {
            $this->abort('www folder is not world readable');
        }
        $this->getOutput()->writeln('OK');

        // folder Core/Logs
        $this->getOutput()->write('Checking the folder <info>Log</info> ... ');
        $logPath = $this->paths['root'] . '/logs';
        if (!file_exists($logPath)) {
            if (!mkdir($logPath)) {
                $this->abort('unable to create the folder Log');
            }
        }
        if (!chmod($logPath, 0777)) {
            $this->abort('unable to make the folder Log world writeable');
        }
        $logPerms = fileperms($logPath);
        if (($logPerms & 0x0006) != 0x0006) {
            $this->abort('logs folder is not world +rw');
        }
        $this->getOutput()->writeln('OK');

        /* TODO: this part has to be rewritten, check of confFile
        // symlink config
        $this->getOutput()->write('Checking the <info>config</info> file ... ');
        $configFile = $this->isStageOrProd() ? $this->nextDBConfigRealPath : $this->dbConfigRealPath;
        if (file_exists($configFile)) {
            $this->abort(sprintf('%s file does not exist', $configFile));
        }
        $perms = fileperms($configFile);
        if (($perms & 0x0020) != 0x0020) {
            $this->abort(sprintf('%s file is not group readable', $configFile));
        }
        $this->getOutput()->writeln("OK");*/
    }

    protected function setDown () {

        $this->setIsDown(true, $this->setDownPath, $this->isDownPath, 'Archipad');
        $this->setIsDown(true, $this->setDownPathArchiweb, $this->isDownPathArchiweb, 'Archiweb');

        $this->clearCache();

    }

    protected function setIsDown ($isDown, $setDownPath, $isDownPath, $product = "") {

        if ($isDown) {

            $this->getOutput()->writeln("Setting $product env as DOWN ... ");

            if ($this->getInput()->getOption('verbose')) {
                $this->getOutput()->writeln(sprintf('<comment>copy %s to %s</comment>', $setDownPath,
                                                    $isDownPath));
            }
            if (!copy($setDownPath, $isDownPath)) {

                $this->getOutput()->writeln("\n<error>Unable to set env down</error>");

                $this->setIsDown(false, $setDownPath, $isDownPath, $product);

                $content = file_get_contents($isDownPath);
                if ($content) {
                    $this->getOutput()->writeln('<error>WARNING ! Below is content of isDown.yml<error>');
                    $this->getOutput()
                         ->writeln('<error>You might want to check that because ENV might be down !<error>');
                    $this->getOutput()->writeln($content, OutputInterface::OUTPUT_RAW);
                    $this->getOutput()->writeln('<error>aborting... !</error>');
                    $this->abort();
                }

            }

        }
        else {

            $this->getOutput()->writeln("Setting $product env as UP ... ");

            if ($this->getInput()->getOption('verbose')) {
                $this->getOutput()->writeln(sprintf('<comment>empty %s</comment>', $isDownPath));
            }
            if (false === file_put_contents($isDownPath, '')) {

                $this->getOutput()->writeln("\n<error>/!\\ /!\\ /!\\ /!\\ CRITICAL ERROR /!\\ /!\\ /!\\ /!\\</error>");
                $this->getOutput()->writeln('<error>ERROR WHILE RE-UPPING ENV !!</error>');
                $this->getOutput()->writeln('<error>YOUR ENV IS DOWN AND YOU NEED TO FIX IT BY YOURSELF !!</error>');
                $this->abort();

            }

        }

        $this->isDown = $isDown;

        $this->getOutput()->writeln("OK\n");

    }

    protected function waitForTransactionsFinish () {

        $i = 15;
        $helper = new ProgressBar($this->getOutput(), $i);

        $this->getOutput()->writeln('Waiting a bit to transactions finish .');
        $helper->start();
        for (; $i > 0; --$i) {
            sleep(1);
            $helper->advance();
        }
        $helper->finish();
        $this->getOutput()->writeln("\nOK\n");

    }

    /**
     * @param string $db
     *
     * @return string
     */
    protected function dumpProdDB ($db) {

        $this->getOutput()->write(sprintf('Dumping current <env>%s</env> %s ... ', $this->getEnv(), $db));

        $dumpPath = $this->dumpFolder . '/' . date('Ymj-His', time()) . '-' . $this->getEnv() . '-' . $db . '-dump.sql';
        $host = escapeshellarg($this->dbConfig['current'][$db]['host']);
        $user = escapeshellarg($this->dbConfig['current'][$db]['user']);
        $password = escapeshellarg($this->dbConfig['current'][$db]['password']);
        $dbname = escapeshellarg($this->dbConfig['current'][$db]['dbname']);
        $passwordCmd = empty($this->dbConfig['current'][$db]['password']) ? '' : '-p' . $password;

        $cmd =
            'mysqldump -h ' . $host . ' -u ' . $user . ' ' . $passwordCmd . ' ' . $dbname . ' > '
            . escapeshellarg($dumpPath);
        if ($this->getInput()->getOption('verbose')) {
            $this->getOutput()->writeln(sprintf('<comment>%s</comment>', $cmd));
        }
        $returnCode = NULL;
        $unused = NULL;
        exec($cmd, $unused, $returnCode);

        if ($returnCode !== 0 || !file_exists($dumpPath) || filesize($dumpPath) == 0) {
            $this->abort(sprintf('Unable to dump <env>%s</env> %s, aborting...', $this->getEnv(), $db));
        }

        $this->getOutput()->writeln("OK\n");

        return $dumpPath;

    }

    /**
     * @param string $db
     */
    protected function clearNextDB ($db) {


        $this->getOutput()->write(sprintf('Clearing future <env>%s</env> %s ... ', $this->getEnv(), $db));

        $cleanDatabaseFile = sys_get_temp_dir() . '/' . uniqid() . '-cleanup.sql';

        file_put_contents($cleanDatabaseFile, "SET FOREIGN_KEY_CHECKS = 0;\n");

        $host = escapeshellarg($this->dbConfig['next'][$db]['host']);
        $user = escapeshellarg($this->dbConfig['next'][$db]['user']);
        $password = escapeshellarg($this->dbConfig['next'][$db]['password']);
        $dbname = escapeshellarg($this->dbConfig['next'][$db]['dbname']);
        $passwordCmd = empty($this->dbConfig['next'][$db]['password']) ? '' : '-p' . $password;

        $returnCode = NULL;
        $_unused = NULL;
        $cmd =
            'mysqldump -h ' . $host . ' -u ' . $user . ' ' . $passwordCmd . ' --add-drop-table --no-data ' . $dbname
            . ' | grep ^DROP >> ' . escapeshellarg($cleanDatabaseFile);
        if ($this->getInput()->getOption('verbose')) {
            $this->getOutput()->writeln(sprintf('<comment>%s</comment>', $cmd));
        }
        system($cmd, $returnCode);

        // Normally, exit status is 0 if selected lines are found and 1 otherwise.
        // But the exit status is 2 if an error occurred, unless the -q or --quiet
        // or --silent option is used and a selected line is found.
        if ($returnCode != 0 && $returnCode != 1) {
            $this->abort(sprintf('Error while clearing future <env>%s</env> %s, aborting...', $this->getEnv(), $db));
        }

        file_put_contents($cleanDatabaseFile, "\nSET FOREIGN_KEY_CHECKS = 1;", FILE_APPEND);

        $cmd =
            'mysql -h ' . $host . ' -u ' . $user . ' ' . $passwordCmd . ' ' . $dbname . ' < '
            . escapeshellarg($cleanDatabaseFile);
        if ($this->getInput()->getOption('verbose')) {
            $this->getOutput()->writeln(sprintf('<comment>%s</comment>', $cmd));
        }
        exec($cmd, $_unused, $returnCode);

        if ($returnCode != 0) {
            $this->abort(sprintf('Error while clearing future <env>%s</env> %s, aborting...', $this->getEnv(), $db));
        }

        $this->getOutput()->writeln("OK\n");

    }

    /**
     * @param string $dumpPath
     * @param string $db
     */
    protected function copyDataToNextDB ($dumpPath, $db) {


        $this->getOutput()->write(sprintf('Copying data to future <env>%s</env> %s ... ', $this->getEnv(), $db));

        $host = escapeshellarg($this->dbConfig['next'][$db]['host']);
        $user = escapeshellarg($this->dbConfig['next'][$db]['user']);
        $password = escapeshellarg($this->dbConfig['next'][$db]['password']);
        $dbname = escapeshellarg($this->dbConfig['next'][$db]['dbname']);
        $passwordCmd = empty($this->dbConfig['next'][$db]['password']) ? '' : '-p' . $password;

        $returnCode = NULL;
        $_unused = NULL;
        $cmd = 'sed \'s/^.*DEFINER=.*$//g\' ' . $dumpPath . ' | mysql -h ' . $host . ' -u ' . $user . ' '
               . $passwordCmd . ' ' . $dbname;
        if ($this->getInput()->getOption('verbose')) {
            $this->getOutput()->writeln(sprintf('<comment>%s</comment>', $cmd));
        }
        exec($cmd, $_unused, $returnCode);

        if ($returnCode != 0) {
            $this->abort(sprintf('Error while copying data to future <env>%s</env> %s', $this->getEnv(), $db));
        }

        $this->getOutput()->writeln("OK\n");

    }

    protected function runUpgradeScripts () {

        $doctrineFolder = $this->paths['root'] . '/doctrine/';
        $itsFolder = $this->paths['root'] . '/its/';

        $folders = ['doctrine' => $doctrineFolder, 'its' => $itsFolder];

        foreach ($folders as $system => $folder) {

            if (!file_exists($folder)) {
                $this->getOutput()->writeln(sprintf('Folder %s not found.', $folder));
                continue;
            }

            $migrationConfig = Yaml::parse(file_get_contents($folder . 'migrations.yml'));

            if (count(glob($folder . $migrationConfig['migrations_directory'] . '/*')) == 0) {

                $this->getOutput()->writeln(sprintf('No database patches detected for %s.', $system));

            }
            else {

                $this->getOutput()->writeln(sprintf('Upgrading future %s <env>%s</env> DB ... ', $system,
                                                    $this->getEnv()));
                $returnCode = NULL;
                $cmd = "cd {$folder} && php doctrine.php migrations:migrate -n";
                if ($this->getInput()->getOption('verbose')) {
                    $this->getOutput()->writeln(sprintf('<comment>%s</comment>', $cmd));
                }
                system($cmd, $returnCode);

                if ($returnCode != 0) {
                    $this->abort(sprintf('Error while upgrading future %s <env>%s</env> DB', $system, $this->getEnv()));
                }

                $this->getOutput()->writeln("\nOK\n");

            }
        }

    }

    protected function changeTheLink () {

        if (!$this->confirm("Symlink will be updated. Are you ready for that ?\n[Y/n] ")) {
            $this->setUp();
            $this->abort('Installation aborted by user');
        }

        if (!$this->isFirstInstall() && $this->isStageOrProd()) {

            $this->getOutput()->write(sprintf("\nBackuping <env>%s</env> link ... ", $this->getEnv()));

            $backupLink = $this->deployDestDir . '.old';
            if (!rename($this->deployDestDir, $backupLink)) {
                $this->abort('Unable to rename current ' . $this->getEnv() . ' link to .old');
            }

            $this->getOutput()->writeln("OK\n");

        }
        elseif (!$this->isFirstInstall()) {

            $this->getOutput()->write(sprintf("\nRemoving <env>%s</env> link ... ", $this->getEnv()));

            if (!unlink($this->deployDestDir)) {
                $this->abort('Unable to removing current ' . $this->getEnv() . ' link');
            }

            $this->getOutput()->writeln("OK\n");

        }

        $this->getOutput()->write(sprintf('Creating <env>%s</env> link ... ', $this->getEnv()));

        if (!symlink($this->paths['root'], $this->deployDestDir)) {

            $this->getOutput()->writeln("<error>Unable to create new link</error>");

            if (!$this->isFirstInstall() && $this->isStageOrProd()) {

                $this->getOutput()->writeln("Rollbacking to old folder");

                if (!rename($backupLink, $this->deployDestDir)) {

                    $this->getOutput()->writeln("<error>UNABLE TO ROLLBACK !!!</error>");
                    $this->getOutput()
                         ->writeln(sprintf("<error>You're in deep shit, no <env>%s</env> anymore !!</error>",
                                           $this->getEnv()));
                    $this->abort('Please fix manually');

                }

            }

            $this->abort(sprintf('WARNING, unable to upgrade %s, setting back up OLD %s', $this->getEnv(),
                                 $this->getEnv()));

        }

        $this->getOutput()->writeln("OK\n");

        $this->clearCache();

    }

    protected function setUp () {

        if ($this->shouldRestoreArchiwebConfigLink) {

            $this->getOutput()->write(sprintf('Restoring config link to <info>%s</info> with the name <info>%s</info> ... ',
                                              $this->dbConfigRealPathArchiweb, $this->dbConfigLinkNameArchiweb));

            if (file_exists($this->dbConfigLinkNameArchiweb) && !unlink($this->dbConfigLinkNameArchiweb)) {
                $this->abort(sprintf('Unable to remove existing config link <info>%s</info>',
                                     $this->dbConfigLinkNameArchiweb));
            }

            if (!symlink($this->dbConfigRealPathArchiweb, $this->dbConfigLinkNameArchiweb)) {
                $this->getOutput()->writeln("<error>UNABLE TO RESTORE ARCHIWEB CONFIG SYMLINK !!!</error>");
                $this->getOutput()
                     ->writeln(sprintf("<error>you're in deep shit, Archipad and Archiweb don't use the same database, no <env>%s</env> anymore !!</error>",
                                       $this->getEnv()));
                $this->abort('Please fix manually');
            }

            $this->getOutput()->writeln("OK\n");

            $this->shouldRestoreArchiwebConfigLink = false;

        }

        $this->setIsDown(false, $this->setDownPath, $this->isDownPath, 'Archipad');
        $this->setIsDown(false, $this->setDownPathArchiweb, $this->isDownPathArchiweb, 'Archiweb');

        $this->clearCache();

    }

    /**
     * @return array
     */
    protected function getEnvConf () {

        if (!isset($this->envConf)) {
            $this->envConf = Yaml::parse(file_get_contents($this->paths['environmentFile']));
        }

        return $this->envConf;
    }

    /**
     *
     */
    protected function generateOptimizedAutoLoader () {

        $this->getOutput()->writeln('Generation of optimized auto-loader...');

        $cmd =
            'cd ' . escapeshellarg($this->paths['root'])
            . '; curl -sS https://getcomposer.org/installer | php; php composer.phar dumpautoload -o';
        if ($this->getInput()->getOption('verbose')) {
            $this->getOutput()->writeln(sprintf('<comment>%s</comment>', $cmd));
        }

        $returnCode = NULL;
        $unused = NULL;

        if ($this->getInput()->getOption('verbose')) {
            passthru($cmd, $returnCode);
        }
        else {
            exec($cmd, $unused, $returnCode);
        }

        if ($returnCode !== 0) {
            $this->getOutput()->writeln('<warning>Unable to generate optimized auto-loader</warning>');
            if (!$this->confirm("<warning>Do you want to continue?\n[Y/n] </warning>")) {
                $this->abort('Installation aborted by user');
            }
        }
        else {
            $this->getOutput()->writeln('OK');
        }

        $this->getOutput()->writeln('');

    }

    /**
     * Touching .htaccess file clears cache because in run.php we check the mtime of .htaccess to clear cache if mtime
     * of .htaccess is more recent than date of cache creation
     */
    protected function clearCache () {

        $htaccessPath = $this->deployDestDir . '/www/.htaccess';
        if ($this->getInput()->getOption('verbose')) {
            $this->getOutput()->writeln(sprintf("<comment>touch %s</comment>\n", $htaccessPath));
        }
        touch($htaccessPath);

        opcache_reset(); // reset cache for cli
    }

}