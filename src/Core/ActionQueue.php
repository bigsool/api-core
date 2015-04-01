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
    public function addAction (Action $action, array $params = []) {

        parent::enqueue([$action, $params]);

    }

} 