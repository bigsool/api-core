<?php

namespace Core\Interaction;


use Archipad\Model\Dependency;
use Archipad\Module\Dependency\ModuleManager;

class DependenciesInteraction extends AbstractInteraction {

    /**
     * @var string
     */
    protected $projectId;

    /**
     * @var Dependency[]
     */
    protected $dependencies;

    /**
     * @param string $projectId
     * @param array $dependencies
     */
    public function __construct ($projectId, $dependencies) {

        parent::__construct("","");

        $this->projectId = $projectId;

        $this->dependencies = $dependencies;

    }

    
    /**
     * @return array
     */
    public function toArray () {

        $form = [];
        $report = [];

        foreach ($this->dependencies as $dependency) {

            if ($dependency->getType() == ModuleManager::FORM_TYPE) {
                $form[] = $dependency->getBundleId();
            }
            elseif ($dependency->getType() == ModuleManager::REPORT_TYPE) {
                $report[] = $dependency->getBundleId();
            }
            else {
                $form[] = $dependency->getBundleId();
            }

        }

        return array_merge(parent::toArray(),['projectId' => $this->projectId,
                                              'dependencies' => ['report' => $report,'form' => $form]]);

    }

}