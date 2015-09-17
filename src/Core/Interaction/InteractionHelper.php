<?php

namespace Core\Interaction;

class InteractionHelper {

    /**
     * Returns an Interaction (or not) for a given feature
     *
     * @param string                 $feature
     * @param InteractionsDefinition $definition
     *
     * @return Interaction|null
     */
    public static function getInteractionForFeature ($feature, InteractionsDefinition $definition) {

        $callables = $definition->getInteractionCallablesForFeature($feature);

        foreach ($callables as $callable) {
            $interaction = call_user_func($callable);
            if ($interaction) {
                return $interaction;
            }
        }

        return NULL;

    }

    /**
     * @param InteractionsDefinition $definition
     *
     * @return Interaction[]
     */
    public static function getInteractions (InteractionsDefinition $definition) {

        $interactions = [];

        foreach ($definition->getFeatures() as $feature) {
            $interactions[$feature] = static::getInteractionForFeature($feature, $definition);
        }

        return $interactions;

    }

}