<?php

namespace Core\Deploy\Command;

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

    protected $dbConfigRealPath;

    protected $nextDBConfigPath;

    protected $nextDBConfigRealPath;

    protected $setDownPath;

    protected $isDownPath;

    protected $deployDestDir;

    protected $dbConfigDirectory = '../../../../../CONFIG';

    protected $dbConfigFilenames = array(
        'config_db_1.php',
        'config_db_2.php',
    );

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

        if ($isStageOrProd) {

            if (!$isFirstInstall) {
                $this->setDown();
                $this->waitForTransactionsFinish();
            }

            $prodDumpPath = $this->dumpProdDB();
            $this->clearNextDB();
            $this->copyDataToNextDB($prodDumpPath);

        }

        $this->runUpgradeScripts();

        $this->changeTheLink();

    }

    /**
     *
     * @param string $env
     *
     * @throws \RunTimeException
     */
    protected function setEnv ($env) {

        parent::setEnv($env);

        $this->setDownPath = $this->paths['root'] . '/include/config/config.setDown.php';
        $this->isDownPath = $this->paths['root'] . '/include/config/config.isDown.php';

        $this->dbConfigDirectory = $this->paths['root'] . '/' . $this->dbConfigDirectory;

        $config = $this->getEnvConf();

        $this->deployDestDir = $config['dest_dir'];

    }

    protected function isFirstInstall (&$deployDestDir = NULL, $verbose = false) {

        $config = $this->getEnvConf();

        if (!file_exists($config['dest_dir'])) {
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

    /**
     * @return array
     */
    protected function getEnvConf () {

        if (!isset($this->envConf)) {
            $this->envConf = Yaml::parse($this->paths['environmentFile']);
        }

        return $this->envConf;
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

            $this->dbConfigRealPath = $this->deployDestDir . '/include/config/envs/config.php';
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

            $this->nextDBConfigPath = $this->paths['root'] . '/include/config/envs/config.php';

            if ($isFirstInstall) {

                $this->nextDBConfigRealPath =
                    $this->dbConfigDirectory . '/' . $this->dbConfigFilenames[1 % count($this->dbConfigFilenames)];
                $this->dbConfig['next'] = $this->loadDBConfig($this->nextDBConfigRealPath);

            }
            else {

                if (!is_link($this->dbConfigRealPath)) {
                    $this->abort(sprintf('The config file %s must be a link, but it is not', $this->paths['dbConfig']));
                }

                $dbConfigPath = realpath($this->dbConfigRealPath);
                $configFilename = basename($dbConfigPath);
                $configDirectory = dirname($dbConfigPath);

                if (($index = array_search($configFilename, $this->dbConfigFilenames)) === false) {
                    $this->abort(sprintf('The config filename %s is not expected', $configFilename));
                }

                $this->nextDBConfigRealPath =
                    $configDirectory . '/' . $this->dbConfigFilenames[($index + 1) % count($this->dbConfigFilenames)];
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
        require $file;
        if (!isset($Config) || !isset($Config['database'])) {
            $this->abort(sprintf('Config cannot be found in the file %s', $file));
        }

        return $Config['database'];

    }

    protected function confirmNextDBName () {

        $output = $this->getOutput();

        $currentDBName = $this->dbConfig['current']['dbname'];
        $currentDBHost = $this->dbConfig['current']['host'];
        $nextDBName = $this->dbConfig['next']['dbname'];
        $nextDBHost = $this->dbConfig['next']['host'];

        $output->writeln(sprintf(
                             'Current <env>%s</env> DB is <info>%s@%s</info>',
                             $this->env, $currentDBName, $currentDBHost
                         ));
        $output->writeln(sprintf(
                             'Next <env>%s</env> DB will be <info>%s@%s</info>',
                             $this->env, $nextDBName, $nextDBHost
                         ));
        $output->writeln(sprintf(
                             "Next <env>%s</env> DB will be TOTALLY ERASEN !\n",
                             $this->env
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

    protected function createConfigLink () {

        $this->getOutput()->write(sprintf('Creating config link to <info>%s</info> ... ', $this->dbConfigRealPath));

        $success = symlink($this->nextDBConfigRealPath, $this->nextDBConfigPath);

        if (!$success) {
            $this->abort('Unable to create config symlink, aborting...');
        }

        $this->getOutput()->writeln('OK');

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
            if (!chmod($logPath, 0777)) {
                $this->abort('unable to make the folder Log world writeable');
            }
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

        $this->setIsDown(true);

    }

    protected function setIsDown ($isDown) {

        if ($isDown) {

            $this->getOutput()->write('Setting env as DOWN ... ');

            if (!copy($this->setDownPath, $this->isDownPath)) {

                $this->getOutput()->writeln("\n<error>Unable to set env down</error>");

                $this->setIsDown(false);

                $content = file_get_contents($this->isDownPath);
                if ($content) {
                    $this->getOutput()->writeln('<error>WARNING ! Below is content of config.isDown.php<error>');
                    $this->getOutput()
                         ->writeln('<error>You might want to check that because ENV might be down !<error>');
                    $this->getOutput()->writeln($content, OutputInterface::OUTPUT_RAW);
                    $this->getOutput()->writeln('<error>aborting... !</error>');
                    $this->abort();
                }

            }

        }
        else {

            $this->getOutput()->write('Setting env as UP ... ');

            if (false === file_put_contents($this->isDownPath, '')) {

                $this->getOutput()->writeln("\n<error>/!\\ /!\\ /!\\ /!\\ CRITICAL ERROR /!\\ /!\\ /!\\ /!\\</error>");
                $this->getOutput()->writeln('<error>ERROR WHILE RE-UPPING ENV !!</error>');
                $this->getOutput()->writeln('<error>YOUR ENV IS DOWN AND YOU NEED TO FIX IT BY YOURSELF !!</error>');
                $this->abort();

            }

        }

        $this->getOutput()->writeln("OK\n");

    }

    protected function waitForTransactionsFinish () {

        $this->getOutput()->write('Waiting a bit to transactions finish .');
        for ($i = 0; $i < 5; ++$i) {
            sleep(1);
            $this->getOutput()->write('.');
        }
        $this->getOutput()->writeln(" OK\n");

    }

    protected function dumpProdDB () {

        $this->getOutput()->write(sprintf('Dumping current <env>%s</env> DB ... ', $this->getEnv()));

        $prodDumpPath = sys_get_temp_dir() . '/' . uniqid() . '-prod-dump.sql';

        $host = escapeshellarg($this->dbConfig['current']['host']);
        $user = escapeshellarg($this->dbConfig['current']['user']);
        $password = escapeshellarg($this->dbConfig['current']['password']);
        $dbname = escapeshellarg($this->dbConfig['current']['dbname']);
        $passwordCmd = empty($this->dbConfig['current']['password']) ? '' : '-p' . $password;

        $cmd =
            'mysqldump -h ' . $host . ' -u ' . $user . ' ' . $passwordCmd . ' ' . $dbname . ' > '
            . escapeshellarg($prodDumpPath);
        $returnCode = NULL;
        $unused = NULL;
        exec($cmd, $unused, $returnCode);

        if ($returnCode !== 0 || !file_exists($prodDumpPath) || filesize($prodDumpPath) == 0) {
            $this->abort('Unable to dump prod data base, aborting...');
        }

        $this->getOutput()->writeln("OK\n");

        return $prodDumpPath;

    }

    protected function clearNextDB () {

        $this->getOutput()->write(sprintf('Clearing future <env>%s</env> DB ... ', $this->getEnv()));

        $cleanDatabaseFile = sys_get_temp_dir() . '/' . uniqid() . '-cleanup.sql';

        file_put_contents($cleanDatabaseFile, "SET FOREIGN_KEY_CHECKS = 0;\n");

        $host = escapeshellarg($this->dbConfig['next']['host']);
        $user = escapeshellarg($this->dbConfig['next']['user']);
        $password = escapeshellarg($this->dbConfig['next']['password']);
        $dbname = escapeshellarg($this->dbConfig['next']['dbname']);
        $passwordCmd = empty($this->dbConfig['next']['password']) ? '' : '-p' . $password;

        $returnCode = NULL;
        $_unused = NULL;
        //$cmd = 'mysqldump -h '.$host.' -u '.$user.' '.$passwordCmd.' --add-drop-table --no-data '.$dbname.' | grep ^DROP | mysql -h '.$host.' -u '.$user.' '.$passwordCmd.' '.$dbname.'';
        $cmd =
            'mysqldump -h ' . $host . ' -u ' . $user . ' ' . $passwordCmd . ' --add-drop-table --no-data ' . $dbname
            . ' | grep ^DROP >> ' . escapeshellarg($cleanDatabaseFile);
        system($cmd, $returnCode);

        // Normally, exit status is 0 if selected lines are found and 1 otherwise.
        // But the exit status is 2 if an error occurred, unless the -q or --quiet
        // or --silent option is used and a selected line is found.
        if ($returnCode != 0 && $returnCode != 1) {
            $this->abort(sprintf('Error while clearing future %s DB, aborting...', $this->getEnv()));
        }

        file_put_contents($cleanDatabaseFile, "\nSET FOREIGN_KEY_CHECKS = 1;", FILE_APPEND);

        $cmd =
            'mysql -h ' . $host . ' -u ' . $user . ' ' . $passwordCmd . ' ' . $dbname . ' < '
            . escapeshellarg($cleanDatabaseFile);
        exec($cmd, $_unused, $returnCode);

        if ($returnCode != 0) {
            $this->abort(sprintf('Error while clearing future %s DB, aborting...', $this->getEnv()));
        }

        $this->getOutput()->writeln("OK\n");

    }

    protected function copyDataToNextDB ($prodDumpPath) {

        $this->getOutput()->write(sprintf('Copying data to future <env>%s</env> DB ... ', $this->getEnv()));

        $host = escapeshellarg($this->dbConfig['next']['host']);
        $user = escapeshellarg($this->dbConfig['next']['user']);
        $password = escapeshellarg($this->dbConfig['next']['password']);
        $dbname = escapeshellarg($this->dbConfig['next']['dbname']);
        $passwordCmd = empty($this->dbConfig['next']['password']) ? '' : '-p' . $password;

        $returnCode = NULL;
        $_unused = NULL;
        $cmd = 'mysql -h ' . $host . ' -u ' . $user . ' ' . $passwordCmd . ' ' . $dbname . ' < ' . $prodDumpPath;

        exec($cmd, $_unused, $returnCode);

        if ($returnCode != 0) {
            $this->abort(sprintf('Error while copying data to future %s DB', $this->getEnv()));
        }

        $this->getOutput()->writeln("OK\n");

    }

    protected function runUpgradeScripts () {

        $this->getOutput()->writeln(sprintf('Upgrading future <env>%s</env> DB ... ', $this->getEnv()));

        $doctrineFolder = $this->paths['root'] . '/doctrine/';
        $returnCode = NULL;
        $cmd = "cd {$doctrineFolder} && php doctrine.php migrations:migrate -n";
        system($cmd, $returnCode);

        if ($returnCode != 0) {
            $this->abort(sprintf('Error while upgrading future %s DB', $this->getEnv()));
        }

        $this->getOutput()->writeln("\nOK\n");

    }

    protected function changeTheLink () {

        if (!$this->confirm("Symlink will be updated. Are you ready for that ?\n[Y/n] ")) {
            $this->abort('Installation aborted by user');
        }

        $this->getOutput()->write(sprintf("\nBackuping <env>%s</env> link ... ", $this->getEnv()));

        if (!$this->isFirstInstall()) {

            $backupLink = $this->deployDestDir . '.old';
            if (!rename($this->deployDestDir, $backupLink)) {
                $this->abort('Unable to rename current ' . $this->getEnv() . ' link to .old');
            }

            $this->getOutput()->writeln("OK\n");

        }
        else {

            $this->getOutput()->writeln("Not applicable (first install)\n");

        }

        $this->getOutput()->write(sprintf('Creating <env>%s</env> link ... ', $this->getEnv()));

        if (!symlink(realpath($this->paths['root'] . '/../../../'), $this->deployDestDir)) {

            $this->getOutput()->writeln("<error>Unable to create new link</error>");

            if (!$this->isFirstInstall()) {

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

    }

    protected function setUp () {

        $this->setIsDown(false);

    }

}