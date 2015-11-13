<?php


namespace Core\Interaction;



abstract class AbstractInteractionsDefinition implements InteractionsDefinition {

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

}