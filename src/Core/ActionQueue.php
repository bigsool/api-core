<?php


namespace Core;


use Core\Action\Action;

class ActionQueue extends \SplQueue {

    /**
     * @return array
     */
    public function dequeue () {

        return parent::dequeue();

    }

    /**
     * @param Action $action
     * @param array  $params
     */
    public function enqueue (Action $action, array $params = []) {

        parent::enqueue([$action, $params]);

    }

} 