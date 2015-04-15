<?php


namespace Core\Field;


use Core\Context\FindQueryContext;
use Core\Registry;

interface ResolvableField {

    /**
     * @param Registry         $registry
     * @param FindQueryContext $ctx
     *
     * @return string[]
     */
    public function resolve (Registry $registry, FindQueryContext $ctx);

    /**
     * @param ResolvableField $field
     *
     * @return bool
     */
    public function isEqual (ResolvableField $field);

    /**
     * @return string
     */
    public function getAlias ();

    /**
     * @param string $alias
     */
    public function setAlias ($alias);

    /**
     * @return string
     */
    public function getValue ();

    /**
     * @return string
     */
    public function getResolvedField ();

    /**
     * @return string
     */
    public function getResolvedEntity ();

}