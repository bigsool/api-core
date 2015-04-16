<?php


namespace Core\Expression;


class KeyPath extends AbstractKeyPath {

    /**
     * @return bool
     */
    public function shouldResolveForAWhere () {

        return true;

    }

}