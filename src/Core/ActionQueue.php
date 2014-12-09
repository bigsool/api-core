<?php


namespace Core;


use Core\Action\Action;

class ActionQueue extends \SplQueue {

    /**
     * @param Action $value
     */
    public function enqueue (Action $value) {

    }

    /**
     * @return Action|void
     */
    public function dequeue() {

    }

} 