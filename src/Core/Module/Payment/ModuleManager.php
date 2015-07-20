<?php
/**
 * Created by PhpStorm.
 * User: bigsool
 * Date: 17/07/15
 * Time: 09:49
 */

namespace Core\Module\Payment;

use Core\Context\ApplicationContext;
use Core\Module\ModuleManager as AbstractModuleManager;


class ModuleManager extends AbstractModuleManager{

    public function getModuleEntitiesName (ApplicationContext &$context) {

        return [
            'Payment'
        ];
    }

}