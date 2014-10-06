<?php


namespace Archiweb\Field;


use Archiweb\Expression\AbstractKeyPath;

class KeyPath extends AbstractKeyPath {

    /**
     * @return bool
     */
    protected function isUsedInExpression () {

        return false;

    }
}