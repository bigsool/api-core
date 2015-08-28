<?php


namespace Core\Field;


use Core\Context\FindQueryContext;
use Core\Expression\AbstractKeyPath;
use Core\Expression\Resolver;
use Core\Registry;

class RelativeField {

    use Resolver {
        Resolver::resolve as protected _resolve;
    }

    /**
     * @var string
     */
    protected $value;

    /**
     * @var string
     */
    protected $alias;

    /**
     * @param string|ResolvableField $value
     */
    public function __construct ($value) {

        if ($value instanceof ResolvableField) {
            $this->value = $value;
        }
        else {

            if (!AbstractKeyPath::isValidKeyPath($value)) {
                throw new \RuntimeException('invalid KeyPath');
            }

            $this->value = $value;
        }

    }

    /**
     * @return string
     */
    public function getValue () {

        return $this->value instanceof ResolvableField ? $this->value->getValue() : $this->value;

    }

    /**
     * @param Registry         $registry
     * @param FindQueryContext $ctx
     *
     * @return ResolvableField[]
     */
    public function resolve (Registry $registry, FindQueryContext $ctx) {

        if ($this->value instanceof ResolvableField) {

            $this->value->setAlias($this->getAlias());

            return [$this->value];

        }

        $isRealField = $this->process($ctx);

        $fields = [];

        $appCtx = $ctx->getApplicationContext();
        $resolvedEntity = $this->getResolvedEntity();
        $resolvedField = $this->getResolvedField();
        if ($isRealField) {

            $components = explode('.', $this->getValue());
            $strComponents = '';
            foreach ($components as $component) {
                if ($strComponents) {
                    $fields[] = new RealField("{$strComponents}.id");
                    $strComponents .= ".{$component}";
                }
                else {
                    $strComponents = $component;
                }

            }

            $field = new RealField($this->getValue());
            $field->setAlias($this->getAlias());
            $fields[] = $field;

        }
        elseif ($appCtx->isCalculatedField($resolvedEntity, $resolvedField)) {

            $field = $appCtx->getCalculatedField($resolvedEntity, $resolvedField);

            // set the value of this field in order to know where we are located in the calculated field
            $field->setBase(substr($this->getValue(),0,strrpos($this->getValue(),'.')));

            if ($field instanceof Aggregate) {
                $fields = [$field];
            }
            elseif ($field instanceof CalculatedField) {
                $fields = $field->getFinalFields($registry, $ctx);
                $fields[] = $field;
            }

        }

        return $fields;

    }

    /**
     * @return bool
     */
    public function shouldResolveForAWhere () {

        return false;

    }

    /**
     * @return bool
     */
    public function shouldThrowExceptionIfFieldNotFound () {

        return false;

    }

    /**
     * @return string
     */
    public function getAlias () {

        return $this->alias;
    }

    /**
     * @param string $alias
     */
    public function setAlias ($alias) {

        $this->alias = $alias;
    }
}