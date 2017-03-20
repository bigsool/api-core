<?php

namespace Core\Interaction;


use Archipad\Model\Dependency;
use Archipad\Module\Dependency\DependencyHelper;
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
     * DependenciesInteraction constructor.
     * @param string $projectId
     * @param string $dependencies
     */
    public function __construct ($projectId, $dependencies) {

        parent::__construct(null,null);

        $this->projectId = $projectId;

        $this->dependencies = $dependencies;

    }

    
    /**
     * @return array
     */
    public function toArray () {

        $form = [];
        $report = [];
        $formBundles = [];
        $reportBundles = [];

        foreach ($this->dependencies as $dependency) {

            $AS3DependencyZip = DependencyHelper::getAS3DependencyZipFromDependency($dependency->getBundleId());

            if (!$AS3DependencyZip) {
                continue;
            }

            $AS3DependencyZipInfos = [
                'bundleId' => $AS3DependencyZip->getBundleId(),
                'versionTag' => $AS3DependencyZip->getVersionTag(),
                'size' => $AS3DependencyZip->getSize(),
                'lastModificationDate' => DependencyHelper::getDateFormattedForClient($AS3DependencyZip->getLastModificationDate()),
            ];

            if ($dependency->getType() == ModuleManager::FORM_TYPE) {
                $form[] = $dependency->getBundleId();
                $formBundles[] = $AS3DependencyZipInfos;
            }
            elseif ($dependency->getType() == ModuleManager::REPORT_TYPE) {
                $report[] = $dependency->getBundleId();
                $reportBundles[] = $AS3DependencyZipInfos;
            }

        }

        return array_merge(parent::toArray(),['projectId' => $this->projectId,
                                              'dependencies' => ['report' => $report,'form' => $form],
                                              'bundles' => ['report' => $reportBundles, 'form' => $formBundles]
        ]);

    }

}