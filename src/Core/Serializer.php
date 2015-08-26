<?php

namespace Core;

use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Module\AggregatedModuleEntity;
use Core\Module\MagicalEntity;
use Core\Util\ArrayExtra;
use Core\Util\ModelConverter;

class Serializer {

    /**
     * @var ActionContext
     */
    protected $actCtx;

    /**
     * @var AggregatedModuleEntity|NULL
     */
    protected $currentAggregatedModuleEntity;

    /**
     * @var bool
     */
    private $inProxyMode = false;

    /**
     * @var array
     */
    private $requiredKeyPaths = [];

    /**
     * @var array
     */
    private $requiredNotFormattedKeyPaths = [];

    /**
     * @var array
     */
    private $requiredFormattedFields = [];

    /**
     * @var array
     */
    private $dataSerialized;

    /**
     * @param ActionContext $actCtx
     */
    public function __construct (ActionContext $actCtx) {

        $this->actCtx = $actCtx;

        $reqCtx = $actCtx->getRequestContext();
        $returnedKeyPaths = $reqCtx->getReturnedFields();

        foreach ($returnedKeyPaths as $keyPath) {
            $value = $keyPath->getValue();
            $this->requiredNotFormattedKeyPaths[] = $value;
            $this->requiredKeyPaths[] = explode('.', $value);
        }

    }

    /**
     * @param AggregatedModuleEntity|NULL $currentAggregatedModuleEntity
     */
    public function setCurrentAggregatedModuleEntity ($currentAggregatedModuleEntity) {

        $this->currentAggregatedModuleEntity = $currentAggregatedModuleEntity;

    }

    /**
     * @return boolean
     */
    public function isInProxyMode () {

        return $this->inProxyMode;
    }

    /**
     * @param boolean $inProxyMode
     */
    public function setInProxyMode ($inProxyMode) {

        $this->inProxyMode = !!$inProxyMode;
    }

    /**
     * @param mixed $data
     *
     * @return Serializer
     */
    public function serialize ($data) {

        $this->actCtx->getApplicationContext()->getTraceLogger()->trace('serialization begin');

        $data = $this->convertDoctrineObjects($data);

        if (is_array($data)) {
            $this->convertDateTime($data);
        }

        if (is_array($data) && !$this->inProxyMode) {
            $this->dataSerialized = $this->removeDoctrineId($this->requiredKeyPaths, $data);
            $this->dataSerialized = $this->keepOnlyRequestedFields($this->dataSerialized);
        }
        else {
            $this->dataSerialized = $data;
        }

        $this->actCtx->getApplicationContext()->getTraceLogger()->trace('serialization end');

        return $this;

    }

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    public function convertDoctrineObjects ($data) {

        if (is_array($data)) {
            array_walk_recursive($data, function (&$data) {

                $data = $this->convertDoctrineObjects($data);

            });
        }

        if (is_object($data)) {
            $applicationContext = ApplicationContext::getInstance();
            if ($applicationContext->isEntity($data)) {
                return (new ModelConverter($applicationContext))->toArray($data, $this->requiredFormattedFields
                    ?: $this->requiredNotFormattedKeyPaths);
            }
            elseif ($data instanceof MagicalEntity) {
                $requestContext = $this->actCtx->getRequestContext();

                $serializerContext = $this->getAggregatedModuleEntity($data)->getAggregatedSerializerContext();
                $returnedFields = $serializerContext->convertRequestedFields($requestContext->getReturnedFields());
                $requestContext->setFormattedReturnedFields($returnedFields);

                foreach ($requestContext->getFormattedReturnedFields() as $formattedField) {
                    $this->requiredFormattedFields[] = $formattedField->getValue();
                }
                $tmpConvertedEntity = $this->convertDoctrineObjects($data->getMainEntity());

                return $serializerContext->formatResultArray([$tmpConvertedEntity])[0];
            }
        }

        return $data;

    }

    /**
     * @param MagicalEntity $model
     *
     * @return AggregatedModuleEntity
     */
    protected function getAggregatedModuleEntity (MagicalEntity $model) {

        $this->actCtx->getApplicationContext()->populateSerializerWithAggregatedModuleEntity($this, $model);

        return $this->currentAggregatedModuleEntity;

    }

    /**
     * @param array $data
     */
    public function convertDateTime (array &$data) {

        array_walk_recursive($data, function (&$value) {

            if ($value instanceof \DateTime) {
                //$value = $value->format($value::ISO8601);
                $timestampMilliseconds = $value->format('U') . '000';
                $value = sprintf('\/Date(%s)/', $timestampMilliseconds);
            }

        });

    }

    /**
     * @param [] String $keyPaths
     * @param [] String $data
     *
     * @return Array
     */
    private function removeDoctrineId ($keyPaths, $data) {

        $newData = [];

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if ($key === "id" && $this->isAutomaticallyAdded($keyPaths)) {
                    continue;
                }
                $newData[$key] = $this->removeDoctrineId($this->getKeyPaths($keyPaths, $key), $value);
            }
        }
        else {
            $newData = $data;
        }

        return $newData;

    }

    /**
     * @param [] String $keyPaths
     *
     * @return boolean
     */
    protected function isAutomaticallyAdded ($keyPaths) {

        foreach ($keyPaths as $keyPath) {
            if ($keyPath[0] === 'id') {
                return false;
            }
        }

        return true;

    }

    /**
     * @param [] String $keyPaths
     * @param mixed $value
     *
     * @return Array
     */
    protected function getKeyPaths ($keyPaths, $value) {

        $newKeyPaths = [];

        foreach ($keyPaths as $keyPath) {

            if ($keyPath[0] === $value) {

                $newKeyPath = [];

                for ($i = 1; $i < count($keyPath); ++$i) {
                    $newKeyPath[] = $keyPath[$i];
                }

                if (count($newKeyPath) != 0) {
                    $newKeyPaths[] = $newKeyPath;
                }

            }

        }

        return $newKeyPaths ? $newKeyPaths : $keyPaths;

    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function keepOnlyRequestedFields ($data) {

        $isAssociative = ArrayExtra::isAssociative($data);
        if ($isAssociative) {
            $data = [$data];
        }

        $newData = [];
        foreach ($data as $currData) {
            if (!is_array($currData)) {
                $newData[] = $currData;
                continue;
            }
            $currNewData = [];
            foreach ($this->actCtx->getRequestContext()->getReturnedFields() as $field) {
                ArrayExtra::magicalSet($currNewData, $field->getValue(),
                                       ArrayExtra::magicalGet($currData, $field->getValue()));
            }
            $newData[] = $currNewData;
        }

        return $isAssociative ? $newData[0] : $newData;

    }

    /**
     * @return string
     */
    public function getJSON () {

        return json_encode($this->dataSerialized);

    }

    /**
     * @return mixed
     */
    public function get () {

        return $this->dataSerialized;

    }

}