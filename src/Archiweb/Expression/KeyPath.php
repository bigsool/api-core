<?php


namespace Archiweb\Expression;


class KeyPath extends AbstractKeyPath {

    /**
     * @return bool
     */
    protected function isUsedInExpression () {

        return true;

    }

}