<?php


namespace Core\Expression;


class KeyPath extends AbstractKeyPath {

    /**
     * @return bool
     */
    protected function isUsedInExpression () {

        return true;

    }

    /**
     * @return bool
     */
    public function shouldResolveForAWhere () {

        return true;

    }
}