<?php


namespace Core;


use Core\Action\Action;

class ActionQueue extends \SplQueue {

    /**
     * @param array $value
     */
    public function enqueue ($value) {

        if (!is_array($value) || !in_array(count($value), [1, 2]) || !($value[0] instanceof Action)
            || (count($value) == 2 && !is_array($value[1]))
        ) {
            throw new \RuntimeException('invalid action queued');
        }

        if (count($value) == 1) {
            $value[] = [];
        }

        parent::enqueue($value);

    }

    /**
     * @return array
     */
    public function dequeue () {

        return parent::dequeue();

    }

} 