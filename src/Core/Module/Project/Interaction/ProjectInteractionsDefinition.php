<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 02/07/15
 * Time: 16:01
 */

namespace Core\Module\Project\Interaction;


interface ProjectInteractionsDefinition {

    /**
     *
     * Returns a list of callable for a given feature
     * Each callable should return an Interaction if appropriate
     *
     * @param string $feature
     *
     * @return callable[]
     */
    public function getInteractionCallablesForFeature($feature);

}