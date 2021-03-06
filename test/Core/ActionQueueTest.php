<?php


namespace Core;


class ActionQueueTest extends TestCase {

    public function testEnqueue () {

        $action1 = $this->getMockAction();
        $action2 = $this->getMockAction();
        $queue = new ActionQueue();
        $queue->addAction($action1);

        $this->assertSame([$action1, []], $queue->dequeue());

        $queue->addAction($action1, ['qwe']);
        $queue->addAction($action2, []);
        $this->assertSame([$action1, ['qwe']], $queue->dequeue());
        $this->assertSame([$action2, []], $queue->dequeue());

    }

} 