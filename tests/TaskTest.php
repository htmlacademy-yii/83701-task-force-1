<?php

namespace Test;

use phpDocumentor\Reflection\Types\This;
use PHPUnit\Framework\TestCase;
use TForce\Task;

class TaskTest extends TestCase {

    /**@var \TForce\Task */
    private $taskInst;

    public function setUp() {
        $this->taskInst = new Task();
    }

    public function testGetAllStatuses() {

        $expectedStatuses = ['NEW', 'CANCELED', 'WORKING', 'DONE', 'FAILED'];
        sort($expectedStatuses);
        $actualStatuses = $this->taskInst->getAllStatuses();
        sort($actualStatuses);
        $this->assertEquals(
            $expectedStatuses, $actualStatuses, 'wrong all Statuses'
        );
    }

    public function nextStatusByCurAction() {

        return [
            ['cancel', 'CANCELED'],
            ['respond', 'WORKING'],
            ['setDone', null],
            ['reject', null]
        ];

    }

    /**
     * @dataProvider nextStatusByCurAction
     */
    public function testGetStatusByAction($action, $expectedStatus) {

        $actualStatus = $this->taskInst->getStatusByAction($action);
        $this->assertEquals($expectedStatus, $actualStatus, 'wrong new status');

    }

    public function testGetCurStatus() {

        $expected = 'NEW';
        $actual = $this->taskInst->getCurStatus();
        $this->assertEquals(
            $expected,
            $actual,
            'first status of task should be NEW'
        );
    }

    public function testGetAllActions() {

        $expectedActions = ['setDone', 'reject', 'cancel', 'respond'];
        sort($expectedActions);
        $actualActions = $this->taskInst->getAllActions();
        sort($actualActions);
        $this->assertEquals(
            $expectedActions, $actualActions, 'wrong all actions'
        );

    }

    public function tearDown() {
        $this->taskInst = null;
    }


}
