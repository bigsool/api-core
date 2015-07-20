<?php

namespace Core\Module\Project\Interaction;


use Core\Interaction\Interaction;
use Core\Module\Project\Interaction\ProjectInteractionsDefinition;

class ProjectInteractionHelper {

    /**
     * Returns an Interaction (or not) for a given feature
     *
     * @param string                        $feature
     * @param ProjectInteractionsDefinition $definition
     *
     * @return Interaction|null
     */
    public static function getInteractionForFeature ($feature, ProjectInteractionsDefinition $definition) {

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
     * @param ProjectInteractionsDefinition $definition
     *
     * @return Interaction[]
     */
    public static function getInteractions (ProjectInteractionsDefinition $definition) {

        $interactions = [];

        foreach ($definition->getFeatures() as $feature) {
            $interactions[$feature] = static::getInteractionForFeature($feature, $definition);
        }

        return $interactions;

    }

}