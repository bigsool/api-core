<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 02/07/15
 * Time: 16:01
 */

namespace Core\Interaction;


interface InteractionsDefinition {

    /**
     *
     * Returns a list of callable for a given feature
     * Each callable should return an Interaction if appropriate
     *
     * @param string $feature
     *
     * @return callable[]
     */
    public function getInteractionCallablesForFeature ($feature);

    /**
     * Return the list of features applicable to Project
     *
     * @return string[]
     */
    public static function getFeatures ();

}