<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 02/07/15
 * Time: 14:14
 */

namespace Core\Interaction;


class ErrorInteraction extends AlertInteraction {

    const TYPE = "error";

    public function getType() {
        return self::TYPE;
    }

}