<?php

namespace Test;

use PHPUnit\Framework\TestCase;
use TForce\Task;

define('ROOT', getcwd());

require_once ROOT . DIRECTORY_SEPARATOR
    . 'vendor' . DIRECTORY_SEPARATOR
    . 'autoload.php';

class TaskTest extends TestCase {

    /**@var \TForce\Task */
    private $taskInst;

    const PREFIX_STATUS = 'STATUS';
    const PREFIX_ACTION = 'ACTION';
    const PREFIX_ROLE = 'ROLE';
    const PREFIX_MAP = 'MAP';
    const PREFIX_USERS = 'USERS';

    public function setUp() {
        $this->taskInst = new Task(2, 3);
    }

    public function tearDown() {
        $this->taskInst = null;
    }

    public static function filterClassConstants($classConstants, $filterPrefix) {
        return array_filter(
            $classConstants,
            function ($constName) use ($filterPrefix) {

                $constPrefix = explode('_', $constName)[0];
                return ($constPrefix === $filterPrefix) ? true : false;

            },
            ARRAY_FILTER_USE_KEY
        );
    }

    public static function getClassConstants($className) {

        $allConstants = (new \ReflectionClass(Task::class))->getConstants();

        return [
            self::PREFIX_STATUS =>
                self::filterClassConstants($allConstants, self::PREFIX_STATUS),
            self::PREFIX_ACTION =>
                self::filterClassConstants($allConstants, self::PREFIX_ACTION),
            self::PREFIX_MAP    =>
                self::filterClassConstants($allConstants, self::PREFIX_MAP)
        ];
    }

    /*
    TODO +
    проверить, что будет ошибка, если не передадим id исполнителя и id
    заказчика
    */
    public function testCreateTaskWithoutCustomerIdOrExecutorId() {
        $this->expectException(\Throwable::class);
        new Task(2);
    }

    /*
    TODO +
    проверить, что при передаче id заказчика и id испонителя будет создана
    задача со статусом 'new'
    */
    public function testStatusOfNewTask() {

        $allStatusConstants =
            self::getClassConstants(Task::class)[self::PREFIX_STATUS];

        $statusConstantNew = array_filter(
            $allStatusConstants,
            function ($oneStatusConstant) {
                return $oneStatusConstant['EN'] === 'new';
            }
        );

        $expected = array_values($statusConstantNew)[0];
        $actual = $this->taskInst->getCurStatus();
        $this->assertEquals($expected, $actual, 'WRONG NEW TFORCE\TASK');
    }

    /*
    TODO +
    проверить, что корректно возвращается статус задачи
    */
    public function dataStatusesForTask() {

        $onlyStatusConstants =
            self::getClassConstants(Task::class)[self::PREFIX_STATUS];

        $dataStatusesForTest = array_reduce(
            $onlyStatusConstants,
            function ($curry, $oneStatusConstant) {
                array_push($curry, [$oneStatusConstant]);
                return $curry;
            },
            array()
        );

        return $dataStatusesForTest;
    }

    /**
     * @dataProvider dataStatusesForTask
     */
    public function testGetCurStatus(array $newStatus) {

        $reflectionClass = new \ReflectionClass(Task::class);
        $reflectionProperty = $reflectionClass->getProperty('curStatus');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this->taskInst, $newStatus);
        $reflectionProperty->setAccessible(false);

        $expected = $newStatus;
        $actual = $this->taskInst->getCurStatus();
        $this->assertEquals(
            $expected,
            $actual,
            "WRONG CURRENT STATUS {$newStatus['EN']} - {$newStatus['RU']}");
    }

    /*
    TODO +
    проверить, что попытке присвоить неправильный статус задаче -
    выкинет ошибку
    */
    public function testSetWrongNewTaskStatus() {
        $this->expectException(\Throwable::class);
        $this->taskInst->setNewStatus(['EN' => '1a2s3d', 'RU' => '1a2s3d']);
    }

    /*
    TODO +
    проверить, что возвращаются список всех коснстант статусов задачи
    */
    public function testGetAllStatuses() {

        $expected = self::getClassConstants(Task::class)[self::PREFIX_STATUS];
        $actual = $this->taskInst->getAllStatuses();

        $this->assertEquals(
            $expected,
            $actual,
            'WRONG RETURNED ALL STATUSES FROM CLASS!'
        );
    }

    /*
    TODO +
    проверить, что возвращаются список всех констант действий задачи
    */
    public function testGetAllActions() {
        $expected = self::getClassConstants(Task::class)[self::PREFIX_ACTION];
        $actual = $this->taskInst->getAllActions();

        $this->assertEquals(
            $expected,
            $actual,
            'WRONG RETURNED ALL ACTIONS FROM CLASS!'
        );
    }

    /*
    TODO +
    проверить, что не нарушена целостность карты MAP_STATUS_ACTION
    */
    public function testStructureMapStatusAction() {

        $expectedMapConstant =
            self::getClassConstants(Task::class)[self::PREFIX_MAP];

        $expectedMapConstantValue = current($expectedMapConstant);

        $expectedStatusConstantsArr = array_values(
            self::getClassConstants(Task::class)[self::PREFIX_STATUS]
        );

        $expectedStatusConstantsStr =
            array_column($expectedStatusConstantsArr, 'EN');

        $expectedActionConstantsArr = array_values(
            self::getClassConstants(Task::class)[self::PREFIX_ACTION]
        );

        $expectedActionConstantsStr =
            array_column($expectedActionConstantsArr, 'EN');

        /*---------------------------------------------------------*/

        $actualMapConstant = $this->taskInst->getMapStatusAction();
        $actualMapConstantValue = current($actualMapConstant);
        $actualStatusesToActions = array_values($actualMapConstantValue);
        $actualStatusesStr = array_keys($actualMapConstantValue);
        $actualActionArrs = array_column($actualMapConstantValue, 'actions');
        $actualActionsStr = array_reduce(
            $actualActionArrs,
            function ($curry, $oneActualActionArr) {
                $curry = array_merge($curry, array_keys($oneActualActionArr));
                return $curry;
            },
            array()
        );

        /*---------------------------------------------------------*/

        // вся карта массив
        $this->assertIsArray(
            $expectedMapConstant, 'MAP_STATUS_ACTION IS NOT ARRAY'
        );

        // основное значение карты под ключом MAP_STATUS_ACTION - массив
        $this->assertIsArray(
            $expectedMapConstantValue, 'MAP_STATUS_ACTION VALUE IS NOT ARRAY'
        );

        // с каждым статусом в карте связан массив действий
        foreach ($actualStatusesToActions as $oneStatusToAction) {
            $this->assertIsArray(
                $oneStatusToAction,
                'STATUS TO ACTION IS NOT ARRAY'
            );
        }

        // статусы в карте 'EN' = константы статусов в классе 'EN'
        foreach ($actualStatusesStr as $oneStatusConstantStr) {
            $this->assertContains(
                $oneStatusConstantStr,
                $expectedStatusConstantsStr
            );
        }

        // actions, которые связаны со статусом - массив
        foreach ($actualActionArrs as $oneActualActionArr) {
            $this->assertIsArray(
                $oneActualActionArr,
                'ACTIONS IS ONT ARRAY'
            );
        }

        // действия в карте 'EN' =  констаты действий в классе 'EN'
        foreach ($actualActionsStr as $oneActionConstantStr) {
            $this->assertContains(
                $oneActionConstantStr,
                $expectedActionConstantsStr
            );
        }

    }


    /*
    TODO +
    проверить, что для указанного статуса возвращается верный список действий
    */
    public function dataActionsForStatus() {

        $dataSet = [];
        $expectedMapConstants =
            current(self::getClassConstants(Task::class)[self::PREFIX_MAP]);
        $allExpectedStatusConstants =
            self::getClassConstants(Task::class)[self::PREFIX_STATUS];
        $allExpectedActionConstants =
            self::getClassConstants(Task::class)[self::PREFIX_ACTION];

        foreach ($expectedMapConstants as $statusKeyEn => $statusInfo) {

            $oneFilteredExpectedStatusConstant =
                array_filter(
                    $allExpectedStatusConstants,
                    function ($oneExpectedStatus) use ($statusKeyEn) {
                        return $oneExpectedStatus['EN'] === $statusKeyEn;
                    }
                );

            $oneFilteredExpectedStatus =
                current($oneFilteredExpectedStatusConstant);

            $actionsByStatusStr = array_keys($statusInfo['actions']);

            if (count($actionsByStatusStr) === 0) {
                array_push(
                    $dataSet,
                    [$oneFilteredExpectedStatus, null]
                );
                continue;
            }

            $actionsByStatus = array_reduce(
                $actionsByStatusStr,

                function ($curry, $oneActionByStatusStr) use ($allExpectedActionConstants) {
                    $oneActionArr = array_filter(

                        $allExpectedActionConstants,
                        function ($oneAction) use ($oneActionByStatusStr) {
                            return $oneAction['EN'] === $oneActionByStatusStr;
                        }

                    );
                    array_push($curry, current($oneActionArr));
                    return $curry;
                },

                array()
            );

            array_push(
                $dataSet,
                [$oneFilteredExpectedStatus, $actionsByStatus]
            );

        };

        return $dataSet;
    }

    /**
     * @dataProvider dataActionsForStatus
     */
    public function testGetActionsByStatus($status, $expectedActions) {
        $actualActions = $this->taskInst->getActionsByStatus($status);
        $this->assertEquals(
            $expectedActions,
            $actualActions,
            "WRONG ACTIONS FOR STATUS - {$status['EN']}"
        );
    }

    /*
TODO -
проверить , что по заданному ACTION возвращается верный STATUS
*/
    public function testGetStatusAfterAction() {
        $this->markTestIncomplete(
            'AWAITING IMPLEMENTATION'
        );
    }


}
