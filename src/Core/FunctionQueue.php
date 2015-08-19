<?php


namespace Core;



class FunctionQueue extends \SplQueue {

    /**
     * @param callable $value
     */
    public function enqueue ($value) {

        if (!is_callable($value)) {
            throw new \InvalidArgumentException('$value must be a callable');
        }

        parent::enqueue($value);

    }

    /**
     * @return callable The value of the dequeued node.
     */
    public function dequeue () {

        return parent::dequeue();

    }

}