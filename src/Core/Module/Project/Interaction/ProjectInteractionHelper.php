<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 02/07/15
 * Time: 15:59
 */

namespace Core\Module\Project;


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
     * @return array
     */
    public static function getInteractions (ProjectInteractionsDefinition $definition) {

        $interactions = [];

        foreach ($definition->getFeatures() as $feature) {
            $interactions[$feature] = static::getInteractionForFeature($feature, $definition);
        }

        return $interactions;

    }

}