<?php


namespace Archiweb\Context;


use Archiweb\RuleManager;
use Doctrine\ORM\EntityManager;

class ApplicationContext {

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var RuleManager
     */
    protected $ruleManager;

    /**
     * @return RuleManager
     */
    public function getRuleManager () {

        return $this->ruleManager;

    }

    /**
     * @param RuleManager $ruleManager
     */
    public function setRuleManager (RuleManager $ruleManager) {

        $this->ruleManager = $ruleManager;

    }

    /**
     * @param EntityManagerReceiver $class
     */
    public function getEntityManager (EntityManagerReceiver $class) {

        $class->setEntityManager($this->entityManager);

    }

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager (EntityManager $entityManager) {

        $this->entityManager = $entityManager;

    }

} 