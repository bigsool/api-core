<?php


namespace Archiweb\Context;


use Doctrine\ORM\EntityManager;

interface EntityManagerReceiver {

    /**
     * @param EntityManager $em
     */
    public function setEntityManager (EntityManager $em);

} 