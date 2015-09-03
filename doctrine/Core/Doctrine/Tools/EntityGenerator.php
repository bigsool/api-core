<?php


namespace Core\Doctrine\Tools;


use Core\Context\ApplicationContext;
use Core\Field\Aggregate;
use Core\Field\CalculatedField;
use Doctrine\Common\Inflector\Inflector;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class EntityGenerator extends \Doctrine\ORM\Tools\EntityGenerator {

    /**
     * @var string
     */
    protected static $constructorMethodTemplate =
        '/**
 * Constructor
 * @internal You don\'t have to explicitly call the constructor of this entity. Use the ModuleEntity instead.
 */
public function __construct()
{
<spaces><collections>
}
';

    /**
     * @var string
     */
    protected static $embeddableConstructorMethodTemplate =
        '/**
 * Constructor
 * @internal You don\'t have to explicitly call the constructor of this entity. Use the ModuleEntity instead.
 *
 * <paramTags>
 */
public function __construct(<params>)
{
<spaces><fields>
}
';

    /**
     * @var string
     */
    protected static $emptyConstructorMethodTemplate =
        '/**
 * Constructor
 * @internal You don\'t have to explicitly call the constructor of this entity. Use the ModuleEntity instead.
 */
public function __construct()
{
}
';

    /**
     * @var string
     */
    protected static $classTemplate =
        '<?php

<namespace>
<useStatement>
<entityAnnotation>
<entityClassName>
{
<spaces>/**
<spaces> * @var \Core\Context\FindQueryContext
<spaces> */
<spaces>protected $findQueryContext;

<entityBody>
}
';

    /**
     * @var string
     */
    protected static $getCollectionMethodTemplate =
        '/**
 * <description>
 *
 * @return <variableType>
 */
public function <methodName>()
{

<spaces>$reqCtx = $this->findQueryContext ? $this->findQueryContext->getRequestContext() : \Core\Context\ApplicationContext::getInstance()->getInitialRequestContext();

<spaces>if (!$this-><fieldName>RestrictedIds) {
<spaces><spaces>$faultedVar = "is".ucfirst("<fieldName>")."Faulted";
<spaces><spaces>if ($this->$faultedVar) {
<spaces><spaces><spaces>$this->$faultedVar = false; // TODO : set to false in the hydrator too
<spaces><spaces><spaces>$reqCtx = $reqCtx->copyWithoutRequestedFields();
<spaces><spaces><spaces>$qryContext = new \Core\Context\FindQueryContext("<targetEntity>", $reqCtx);
<spaces><spaces><spaces>$qryContext->addFields("id","<inversedBy>");
<spaces><spaces><spaces>$qryContext->addFilter(new \Core\Filter\StringFilter("<targetEntity>","","<inversedBy>.id = :id"), $this->getId());
<spaces><spaces><spaces>$qryContext->findAll();
<spaces><spaces><spaces>// this query will hydrate <entity> and <targetEntity>
<spaces><spaces><spaces>// RestrictedObjectHydrator will automatically hydrate <fieldName>RestrictedId
<spaces><spaces><spaces>// Since Doctrine shares model instances, <fieldName>RestrictedId will be automatically available
<spaces><spaces>}
<spaces>}

<spaces>$inExpr = \Doctrine\Common\Collections\Criteria::expr()->in("id", $this-><fieldName>RestrictedIds);

<spaces>$criteria = \Doctrine\Common\Collections\Criteria::create();
<spaces>$criteria->where($inExpr);

<spaces>return $this-><fieldName>->matching($criteria);
}';

    /**
     * @var string
     */
    protected static $getAssociationMethodTemplate =
        '/**
 * <description>
 *
 * @return <variableType>
 */
public function <methodName>()
{

<spaces>$reqCtx = $this->findQueryContext ? $this->findQueryContext->getRequestContext() : \Core\Context\ApplicationContext::getInstance()->getInitialRequestContext();

<spaces>if (!$this-><fieldName>RestrictedId) {
<spaces><spaces>$faultedVar = "is".ucfirst("<fieldName>")."Faulted";
<spaces><spaces>if (!$this->$faultedVar) {
<spaces><spaces><spaces>return NULL;
<spaces><spaces>}
<spaces><spaces>$this->$faultedVar = false; // TODO : set to false in the hydrator too
<spaces><spaces>$reqCtx = $reqCtx->copyWithoutRequestedFields();
<spaces><spaces>$qryContext = new \Core\Context\FindQueryContext("<targetEntity>", $reqCtx);
<spaces><spaces>$qryContext->addFields("id","<inversedBy>");
<spaces><spaces>$qryContext->addFilter(new \Core\Filter\StringFilter("<targetEntity>","","<inversedBy>.id = :id"), $this->getId());
<spaces><spaces>$qryContext->findAll();
<spaces><spaces>// this query will hydrate <entity> and <targetEntity>
<spaces><spaces>// RestrictedObjectHydrator will automatically hydrate <fieldName>RestrictedId
<spaces><spaces>// Since Doctrine shares model instances, <fieldName>RestrictedId will be automatically available
<spaces>}

<spaces>return $this-><fieldName> && $this-><fieldName>->getId() == $this-><fieldName>RestrictedId ? $this-><fieldName> : NULL;
}';

    /**
     * @var string
     */
    protected static $getCalculatedFieldMethodTemplate =
        '/**
 * <description>
 *
 * @return <variableType>
 */
public function <methodName>()
{
<spaces>$class = get_class($this);
<spaces>$entity = ($pos = strrpos($class, "\\\\")) ? substr($class, $pos + 1) : $class;
<spaces>$appCtx = \Core\Context\ApplicationContext::getInstance();

<spaces>return $appCtx->getCalculatedField($entity, "<fieldName>")->execute($this);
}';

    /**
     * @var string
     */
    protected static $aggregatedPropertyTemplate =
        '/**
 * @var <variableType>
 */
protected $<fieldName>;';

    /**
     * @var string
     */
    protected static $getAggregatedMethodTemplate =
        '/**
 * <description>
 *
 * @return <variableType>
 */
public function <methodName>()
{
<spaces>return $this-><fieldName>;
}';

    /**
     * @var string
     */
    protected static $setAggregatedMethodTemplate =
        '/**
 * <description>
 *
 * @param <variableType> $<variableName>
 *
 * @return <entity>
 */
public function <methodName>(<methodTypeHint>$<variableName><variableDefault>)
{
<spaces>$this-><fieldName> = $<variableName>;

<spaces>$class = get_class($this);
<spaces>$entity = ($pos = strrpos($class, "\\\\")) ? substr($class, $pos + 1) : $class;
<spaces>$appCtx = \Core\Context\ApplicationContext::getInstance();

<spaces>$this-><fieldName> = $appCtx->getCalculatedField($entity, "<fieldName>")->execute($this);

<spaces>return $this;
}';

    /**
     * @var string
     */
    protected static $setAssociationMethodTemplate =
        '/**
 * <description>
 *
 * @param <variableType> $<variableName>
 *
 * @return <entity>
 */
public function <methodName>(<methodTypeHint>$<variableName><variableDefault>)
{
<spaces>$this-><fieldName> = $<variableName>;
<spaces>$this-><fieldName>RestrictedId = $<variableName> ? $<variableName>->getId() : NULL;

<spaces>return $this;
}';

    /**
     * @var string
     */
    protected static $addCollectionMethodTemplate =
        '/**
 * <description>
 *
 * @param <variableType> $<variableName>
 *
 * @return <entity>
 */
public function <methodName>(<methodTypeHint>$<variableName>)
{
<spaces>$this-><fieldName>[] = $<variableName>;
<spaces>$this-><fieldName>RestrictedIds[] = $<variableName>->getId();

<spaces>return $this;
}';

    /**
     * @var string
     */
    protected static $removeCollectionMethodTemplate =
        '/**
 * <description>
 *
 * @param <variableType> $<variableName>
 */
public function <methodName>(<methodTypeHint>$<variableName>)
{
<spaces>$this-><fieldName>->removeElement($<variableName>);
<spaces>$this-><fieldName>RestrictedIds = array_diff($this-><fieldName>RestrictedIds,[$<variableName>->getId()]);
}';

    /**
     * @param ClassMetadataInfo $metadata
     *
     * @return string
     */
    protected function generateEntityAssociationMappingProperties (ClassMetadataInfo $metadata) {

        $lines = array();

        foreach ($metadata->associationMappings as $associationMapping) {
            $fieldName = $associationMapping['fieldName'];
            if ($this->hasProperty($fieldName, $metadata)) {
                continue;
            }

            $lines[] = $this->generateAssociationMappingPropertyDocBlock($associationMapping, $metadata);
            $lines[] = $this->spaces . $this->fieldVisibility . ' $' . $fieldName
                       . ($associationMapping['type'] == 'manyToMany' ? ' = array()' : NULL) . ";\n";

            $lines[] = $this->spaces . '/**';
            $lines[] =
                $this->spaces . ' * @var int' . ($metadata->isCollectionValuedAssociation($fieldName) ? '[]' : '');
            $lines[] = $this->spaces . ' */';
            $lines[] = $this->spaces . $this->fieldVisibility . ' $' . $fieldName . 'RestrictedId'
                       . ($metadata->isCollectionValuedAssociation($fieldName) ? 's = []' : '') . ";\n";

            $lines[] = $this->spaces . '/**';
            $lines[] = $this->spaces . ' * @var bool';
            $lines[] = $this->spaces . ' */';
            $lines[] = $this->spaces . $this->fieldVisibility . ' $is' . ucfirst($fieldName) . "Faulted = true;\n";
        }

        return implode("\n", $lines);
    }

    /**
     * {@inheritDoc}
     */
    protected function generateEntityStubMethod (ClassMetadataInfo $metadata, $type, $fieldName, $typeHint = NULL,
                                                 $defaultValue = NULL) {

        if (!$metadata->isCollectionValuedAssociation($fieldName)
            && !$metadata->isSingleValuedAssociation($fieldName)
        ) {
            return parent::generateEntityStubMethod($metadata, $type, $fieldName, $typeHint, $defaultValue);
        }

        $restrictedOptions = $type == 'get' ? [true, false] : [true];

        $methods = [];

        foreach ($restrictedOptions as $restricted) {

            $methodName = $type . ($restricted ? '' : 'Unrestricted') . Inflector::classify($fieldName);
            $variableName = Inflector::camelize($fieldName);
            if (in_array($type, array("add", "remove"))) {
                $methodName = Inflector::singularize($methodName);
                $variableName = Inflector::singularize($variableName);
            }

            if ($this->hasMethod($methodName, $metadata)) {
                return '';
            }
            $this->staticReflection[$metadata->name]['methods'][] = strtolower($methodName);

            $collectionOrAssociation =
                $restricted
                    ? ($metadata->isCollectionValuedAssociation($fieldName) ? 'Collection' : 'Association')
                    : '';
            $var = sprintf('%s%sMethodTemplate', $type, $collectionOrAssociation);
            $template = static::$$var;

            $methodTypeHint = NULL;
            $types = Type::getTypesMap();
            $variableType = $typeHint ? $this->getType($typeHint) : NULL;

            if ($typeHint && !isset($types[$typeHint])) {
                $variableType = '\\' . ltrim($variableType, '\\');
                $methodTypeHint = '\\' . $typeHint . ' ';
            }

            $associationMapping = $metadata->getAssociationMapping($fieldName);
            $explodedTargetEntity = explode('\\', $associationMapping['targetEntity']);
            $targetEntity = end($explodedTargetEntity);

            $inversedBy = $associationMapping['inversedBy'] ?: $associationMapping['mappedBy'];
            $replacements = array(
                '<description>'     => ucfirst($type) . ' ' . $variableName,
                '<methodTypeHint>'  => $methodTypeHint,
                '<variableType>'    => $variableType,
                '<variableName>'    => $variableName,
                '<methodName>'      => $methodName,
                '<fieldName>'       => $fieldName,
                '<inversedBy>'      => $inversedBy,
                '<variableDefault>' => ($defaultValue !== NULL) ? (' = ' . $defaultValue) : '',
                '<entity>'          => $this->getClassName($metadata),
                '<targetEntity>'    => $targetEntity,
            );

            $method = str_replace(
                array_keys($replacements),
                array_values($replacements),
                $template
            );

            $methods[] = $this->prefixCodeWithSpaces($method);

        }


        return implode("\n\n", $methods);

    }

    /**
     * {@inheritdoc}
     */
    protected function generateEntityStubMethods (ClassMetadataInfo $metadata) {

        $methods = parent::generateEntityStubMethods($metadata);

        $className = $this->getClassName($metadata);

        $fields = ApplicationContext::getInstance()->getCalculatedFields($className);

        foreach (['get', 'set'] as $type) {
            foreach ($fields as $fieldName => $field) {
                $methodName = $type . Inflector::classify($fieldName);
                $variableName = Inflector::camelize($fieldName);

                if ($this->hasMethod($methodName, $metadata)) {
                    return '';
                }
                $this->staticReflection[$metadata->name]['methods'][] = strtolower($methodName);

                if ($field instanceof CalculatedField) {
                    if ($type == 'set') {
                        continue;
                    }
                    $template = static::$getCalculatedFieldMethodTemplate;
                }
                elseif ($field instanceof Aggregate) {
                    if ($type == 'set') {
                        $template = static::$setAggregatedMethodTemplate;
                    }
                    else {
                        $template = static::$aggregatedPropertyTemplate;
                        $template .= "\n\n";
                        $template .= static::$getAggregatedMethodTemplate;
                    }
                }
                else {
                    throw new \RuntimeException('unexcepted CalculatedField');
                }

                $replacements = array(
                    '<description>'     => ucfirst($type) . ' ' . $variableName,
                    '<methodTypeHint>'  => NULL,
                    '<variableType>'    => 'mixed',
                    '<variableName>'    => $variableName,
                    '<methodName>'      => $methodName,
                    '<fieldName>'       => $fieldName,
                    '<variableDefault>' => '',
                    '<entity>'          => $this->getClassName($metadata)
                );

                $method = str_replace(
                    array_keys($replacements),
                    array_values($replacements),
                    $template
                );

                $methods .= "\n\n" . $this->prefixCodeWithSpaces($method);
            }

        }

        return $methods;

    }

    /**
     * {@inheritdoc}
     */
    protected function generateEntityConstructor (ClassMetadataInfo $metadata) {

        $constructor = parent::generateEntityConstructor($metadata);

        if (!$constructor) {
            $constructor = $this->prefixCodeWithSpaces(static::$emptyConstructorMethodTemplate);
        }

        return $constructor;

    }

}