<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 02/07/15
 * Time: 14:10
 */

namespace Core\Interaction;


interface Interaction {

    public function getType();

    public function getTopic();

    public function getMessage();

}