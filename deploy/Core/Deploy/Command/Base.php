<?php

namespace Core\Deploy\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Helper\QuestionHelper;

abstract class Base extends Command {

    protected $input;

    protected $output;

    protected $question;

    protected $env;

    protected $paths = array();

    protected function configure () {

        $this->addArgument(
            'env',
            InputArgument::REQUIRED,
            'Environment'
        );

        $this->paths['root'] = realpath(__DIR__ . '/../../..');

    }

    /**
     *
     * @return QuestionHelper
     */
    protected function getQuestion () {

        return $this->question;

    }

    /**
     *
     * @param QuestionHelper $question
     */
    protected function setQuestion (QuestionHelper $question) {

        $this->question = $question;

    }

    /**
     *
     * @return string
     */
    protected function getEnv () {

        return $this->env;

    }

    /**
     *
     * @return InputInterface
     */
    protected function getInput () {

        return $this->input;

    }

    /**
     *
     * @param InputInterface $input
     */
    protected function setInput (InputInterface $input) {

        $this->input = $input;

    }

    /**
     *
     * @return OutputInterface
     */
    protected function getOutput () {

        return $this->output;

    }

    /**
     *
     * @param OutputInterface $output
     */
    protected function setOutput (OutputInterface $output) {

        $envStyle = new OutputFormatterStyle('yellow');
        $revStyle = new OutputFormatterStyle('cyan');
        $output->getFormatter()->setStyle('env', $envStyle);
        $output->getFormatter()->setStyle('rev', $revStyle);

        $this->output = $output;

    }

    /**
     *
     * @param string $why
     *
     * @throws \RunTimeException
     */
    protected function abort ($why = NULL) {

        throw new \RunTimeException($why);

    }

    protected function isStageOrProd () {

        return in_array($this->getEnv(), array('prod', 'stage'));

    }

    /**
     *
     * @param string $env
     *
     * @throws \RunTimeException
     */
    protected function setEnv ($env) {

        $env = strtolower($env);
        if (!in_array($env, array('dev', 'stage', 'prod'))) {
            $this->abort(sprintf('Wrong environment : %s - expected once of these value : dev, stage, prod', $env));
        }

        $this->env = $env;

    }

    /**
     *
     * @param string $msg
     *
     * @return bool
     */
    protected function confirm ($msg) {

        return $this->getQuestion()->ask($this->getInput(), $this->getOutput(), new ConfirmationQuestion($msg, false));

    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute (InputInterface $input, OutputInterface $output) {

        $this->setInput($input);
        $this->setOutput($output);
        $this->setEnv($input->getArgument('env'));
        $this->setQuestion($this->getHelper('dialog'));

    }

}