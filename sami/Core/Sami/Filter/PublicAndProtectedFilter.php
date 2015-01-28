<?php

namespace Core\Sami\Filter;


use Sami\Parser\Filter\FilterInterface;
use Sami\Reflection\ClassReflection;
use Sami\Reflection\MethodReflection;
use Sami\Reflection\PropertyReflection;

class PublicAndProtectedFilter implements FilterInterface {

    /**
     * @inheritdoc
     */
    public function acceptClass (ClassReflection $class) {

        return true;

    }

    /**
     * @inheritdoc
     */
    public function acceptMethod (MethodReflection $method) {

        return $method->isPublic() || $method->isProtected();

    }

    /**
     * @inheritdoc
     */
    public function acceptProperty (PropertyReflection $property) {

        return $property->isPublic() || $property->isProtected();

    }
}