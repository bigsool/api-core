<?php


namespace Core\Expression;


class KeyPath extends AbstractKeyPath {

    /**
     * @return bool
     */
    protected function isUsedInExpression () {

        return true;

    }

}