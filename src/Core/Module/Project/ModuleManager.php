<?php
/**
 * Created by PhpStorm.
 * User: bigsool
 * Date: 19/06/15
 * Time: 15:08
 */

namespace Core\Module\Project;

use Core\Context\ApplicationContext;
use Core\Module\ModuleManager as AbstractModuleManager;

class ModuleManager extends AbstractModuleManager{

    public function getModuleEntitiesName (ApplicationContext &$context) {

        return [
            'Project',
            'ProjectPatches',
        ];
    }

}