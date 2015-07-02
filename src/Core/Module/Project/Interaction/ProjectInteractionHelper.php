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
     * @param                               $feature
     * @param ProjectInteractionsDefinition $definition
     *
     * @return Interaction|null
     */
    static public function getInteractionForFeature($feature, ProjectInteractionsDefinition $definition) {
        $callables = $definition->getInteractionCallablesForFeature($feature);

        foreach ($callables as $callable) {
            $interaction = call_user_func($callable);
            if ( $interaction )
                return $interaction;
        }

        return null;

    }

}