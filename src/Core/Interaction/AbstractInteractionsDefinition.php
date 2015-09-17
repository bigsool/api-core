<?php


namespace Core\Interaction;


use Archipad\Model\Client;
use Archipad\Module\Project\Interaction\ArchipadBinaryGoPremium;
use Archipad\Module\Project\Interaction\ArchipadEntBinaryGoPremium;
use Archipad\Module\Project\Interaction\GoPremium;
use Archipad\Module\Project\Interaction\StripeGoPremium;

abstract class AbstractInteractionsDefinition implements InteractionsDefinition {

    /**
     * @return Client
     */
    protected abstract function getClient();

    /**
     * @param string[] $names
     *
     * @return callable[]
     */
    protected function makeCallables (array $names) {

        $callables = [];
        foreach ($names as $name) {
            $callables[] = function () use ($name) {

                $name = lcfirst($name) . 'Interaction';

                return $this->$name();

            };
        }

        return $callables;

    }

    /**
     * @param string $topic
     * @param string $message
     *
     * @return GoPremium
     */
    protected function newGoPremiumInteraction ($topic, $message) {

        switch ($this->getClient()->getName()) {
            case 'archipad':
                return new ArchipadBinaryGoPremium($topic, $message);
            case 'archipad-enterprise':
                return new ArchipadEntBinaryGoPremium($topic, $message);
            default:
                return new StripeGoPremium($topic, $message);
        }

    }

}