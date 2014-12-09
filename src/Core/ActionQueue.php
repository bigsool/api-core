<?php


namespace Core;


use Core\Action\Action;

class ActionQueue extends \SplQueue {

    /**
     * @param Action $value
     */
    public function enqueue ($value) {

        if (!($value instanceof Action)) {
            throw new \RuntimeException('$value must be an Action');
        }

        parent::enqueue($value);

    }

    /**
     * @return Action|void
     */
    public function dequeue () {

        return parent::dequeue();

    }

} 