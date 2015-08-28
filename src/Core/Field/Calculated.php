<?php


namespace Core\Field;


interface Calculated extends ResolvableField {

    /**
     * @param string $base
     */
    public function setBase($base);

    /**
     * @return string
     */
    public function getBase();

    /**
     * @return string
     */
    public function getFieldName();

    /**
     * @param string $fieldName
     */
    public function setFieldName($fieldName);

    /**
     * @param mixed $model
     *
     * @return mixed
     */
    public function execute(&$model);

}