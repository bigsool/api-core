<?php


namespace Archiweb\Operator;


class NotEqualOperator implements CompareOperator
{

    /**
     * @param null $value
     * @return string
     * @throws \RuntimeException
     */
    public function toDQL($value = NULL)
    {
        if (!is_string($value)) {
            throw new \RuntimeException('invalid format');
        }

        return "!= $value";
    }

}