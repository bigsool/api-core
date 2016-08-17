<?php

namespace Core\Interaction;


use Archipad\Model\Dependency;

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
        foreach ($this->dependencies as $dependency) {
            $form[] = $dependency->getBundleId();
        }

        return array_merge(parent::toArray(),['projectId' => $this->projectId,
                                              'dependencies' => ['report' => [],'form' => $form]]);
    }

}