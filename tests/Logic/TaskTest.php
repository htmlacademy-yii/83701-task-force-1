<?php

namespace Test;

use PHPUnit\Framework\TestCase;
use TForce\Logic\Task;
use TForce\Actions\{
    ActionCancel, ActionComplete, ActionReject, ActionRespond
};

define('ROOT', getcwd());

require_once ROOT . DIRECTORY_SEPARATOR . 'vendor' .
    DIRECTORY_SEPARATOR . 'autoload.php';

/**
 * Class TaskTest
 * @package Test
 */
class TaskTest extends TestCase
{

    /** @var \TForce\Logic\Task */
    private $taskInst;
    private $test_executor_id;
    private $test_customer_id;

    const PREFIX_STATUS = 'STATUS';
    const PREFIX_STATUSES = 'STATUSES';
    const PREFIX_ACTION = 'ACTION';
    const PREFIX_ACTIONS = 'ACTIONS';
    const PREFIX_ROLE = 'ROLE';
    const PREFIX_ROLES = 'ROLES';
    const PREFIX_MAP = 'MAP';


    public function setUp()
    {
        $this->test_customer_id = 2;
        $this->test_executor_id = 3;
        $this->taskInst = new Task($this->test_customer_id, $this->test_executor_id);
    }

    public function tearDown()
    {
        $this->taskInst = null;
    }

    /**
     * @param array $classConstants
     * @param string $filterPrefix
     * @return array Constant's collection of certain type
     */
    public static function filterClassConstants($classConstants, $filterPrefix)
    {
        return array_filter(
            $classConstants,
            function ($constName) use ($filterPrefix) {
                $constPrefix = explode('_', $constName)[0];
                return ($constPrefix === $filterPrefix) ? true : false;
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * @param string $className
     * @return array All Constant's collections
     */
    public static function getClassConstants($className)
    {

        $allConstants = (new \ReflectionClass(Task::class))->getConstants();

        return [
            self::PREFIX_STATUS   =>
                self::filterClassConstants($allConstants, self::PREFIX_STATUS),
            self::PREFIX_STATUSES =>
                self::filterClassConstants($allConstants, self::PREFIX_STATUSES),
            self::PREFIX_ACTION   =>
                self::filterClassConstants($allConstants, self::PREFIX_ACTION),
            self::PREFIX_ACTIONS  =>
                self::filterClassConstants($allConstants, self::PREFIX_ACTIONS),
            self::PREFIX_MAP      =>
                self::filterClassConstants($allConstants, self::PREFIX_MAP)
        ];
    }

    public function testCreateTaskWithoutCustomerIdOrExecutorId()
    {
        $this->expectException(\Throwable::class);
        new Task(2);
    }

    public function testStatusOfNewTask()
    {
        $expected = 'new';
        $actual = mb_strtolower($this->taskInst->getCurStatus());
        $this->assertEquals(
            $expected,
            $actual,
            'WRONG STATUS OF NEW TForce\Logic\TASK'
        );
    }

    /**
     * @return array DataSet for 'testGetCurStatus' test
     */
    public function dataStatusesForTask()
    {

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
     * @param string $newStatus
     * @dataProvider dataStatusesForTask
     */
    public function testGetCurStatus(string $newStatus)
    {

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
            "WRONG CURRENT STATUS $newStatus"
        );
    }

    public function testGetAllStatuses()
    {

        $expected = current(
            self::getClassConstants(Task::class)[self::PREFIX_STATUSES]
        );

        $actual = $this->taskInst->getAllStatuses();

        $this->assertEquals(
            $expected,
            $actual,
            'WRONG RETURNED ALL STATUSES FROM CLASS!'
        );
    }

    public function testGetAllActions()
    {

        $expected = $this->taskInst->actionObjects;

        $actual = $this->taskInst->getAllActions();

        $this->assertEquals(
            $expected,
            $actual,
            'WRONG RETURNED ALL ACTIONS FROM CLASS!'
        );
    }

    /**
     * @return array DataSet for 'testGetActionsByStatus' test
     */
    public function dataActionsForStatus()
    {

        $dataSet = [];
        $expectedMapConstants =
            self::getClassConstants(Task::class)[self::PREFIX_MAP];

        $statusActionConstants = current(
            array_filter(
                $expectedMapConstants,
                function ($nameConstant) {
                    $wordsOfNameConstant = explode('_', $nameConstant);
                    return $wordsOfNameConstant[1] === self::PREFIX_STATUS;
                },
                ARRAY_FILTER_USE_KEY
            )
        );

        foreach ($statusActionConstants as $status => $stringActions) {
            array_push($dataSet, [$status, $stringActions]);
        }

        return $dataSet;
    }

    /**
     * @param string $status
     * @param array $expectedStringActions
     * @dataProvider dataActionsForStatus
     */
    public function testGetActionsByStatusWithAnotherId($status, $expectedStringActions)
    {
        $anotherUserId = -1;
        $actualObjActions = $this->taskInst->getActionsByStatus($anotherUserId, $status);

        $this->assertEquals(
            [],
            $actualObjActions,
            "WRONG ACTIONS FOR STATUS - $status"
        );
    }

    /**
     * @param string $status
     * @param array $expectedStringActions
     * @dataProvider dataActionsForStatus
     */
    public function testGetActionsByStatusWithExecutorId($status, $expectedStringActions)
    {

        if ($status === Task::STATUS_NEW) {
            $expectedObjActions = [
                Task::ACTION_RESPOND => new ActionRespond()
            ];


        } else if ($status =Task::STATUS_WORKING) {
            $expectedObjActions = [
                Task::ACTION_REJECT => new ActionReject()
            ];


        } else {
            $expectedObjActions = [];
        }

        $actualObjActions = $this->taskInst->getActionsByStatus(
            $this->test_executor_id, $status
        );

        $this->assertEquals(
            $expectedObjActions,
            $actualObjActions,
            "WRONG ACTIONS FOR STATUS - $status"
        );

    }

    /**
     * @param string $status
     * @param array $expectedStringActions
     * @dataProvider dataActionsForStatus
     */
    public function testGetActionsByStatusWithCustomerId($status, $expectedStringActions)
    {
        if ($status === Task::STATUS_NEW) {
            $expectedObjActions = [
                Task::ACTION_CANCEL => new ActionCancel()
            ];
        } else if ($status === Task::STATUS_WORKING) {
            $expectedObjActions = [
                Task::ACTION_COMPLETE => new ActionComplete()
            ];
        } else {
            $expectedObjActions = [];
        }

        $actualObjActions = $this->taskInst->getActionsByStatus(
            $this->test_customer_id, $status
        );

        $this->assertEquals(
            $expectedObjActions,
            $actualObjActions,
            "WRONG ACTIONS FOR STATUS - $status"
        );
    }

    /**
     * @return array DataSet for 'testGetStatusAfterAction' test
     */
    public function dataStatusForAction()
    {

        $dataSet = [];
        $expectedMapConstants =
            self::getClassConstants(Task::class)[self::PREFIX_MAP];

        $actionStatusConstants = current(
            array_filter(
                $expectedMapConstants,
                function ($nameConstant) {
                    $wordsOfNameConstant = explode('_', $nameConstant);
                    return $wordsOfNameConstant[1] === self::PREFIX_ACTION;
                },
                ARRAY_FILTER_USE_KEY
            )
        );

        foreach ($actionStatusConstants as $action => $status) {
            array_push($dataSet, [$action, $status]);
        }


        return $dataSet;
    }

    /**
     * @param string $action
     * @param string $expectedStatus
     * @dataProvider dataStatusForAction
     */
    public function testGetStatusAfterAction($action, $expectedStatus)
    {

        $actualStatus = $this->taskInst->getStatusAfterAction($action);
        $this->assertEquals(
            $expectedStatus,
            $actualStatus,
            "WRONG STATUS $actualStatus AFTER ACTION $action"
        );

    }

    public function testStructureMapStatusActions()
    {

        $actualMapStatusActions = $this->taskInst->getMapStatusAction();
        $this->assertIsArray(
            $actualMapStatusActions,
            'MAP STATUS ACTIONS MUST BE ARRAY'
        );

        foreach ($actualMapStatusActions as $arrActions) {
            $this->assertIsArray($arrActions, 'ACTIONS IN MAP MUST BE ARRAY');
        }

        $expectedStatuses = array_values(
            self::getClassConstants(Task::class)[self::PREFIX_STATUS]
        );

        $actualStatusesFromMap = array_keys($actualMapStatusActions);

        sort($expectedStatuses);
        sort($actualStatusesFromMap);

        $this->assertEquals(
            $expectedStatuses,
            $actualStatusesFromMap,
            'WRONG INITIAL KEYS IN MAP STATUS ACTIONS'
        );

    }

    public function testStructureMapActionStatus()
    {

        $actualMapActionStatus = $this->taskInst->getMapActionStatus();
        $this->assertIsArray(
            $actualMapActionStatus,
            'MAP ACTION STATUS MUST BE ARRAY'
        );

        foreach ($actualMapActionStatus as $status) {
            $this->assertIsString($status, 'STATUS MUST BE STRING');
        }

        $expectedActions = array_values(
            self::getClassConstants(Task::class)[self::PREFIX_ACTION]
        );
        $actualActionsFromMap = array_keys($actualMapActionStatus);

        sort($expectedActions);
        sort($actualActionsFromMap);

        $this->assertEquals(
            $expectedActions,
            $actualActionsFromMap,
            'WRONG INITIAL KEYS IN MAP ACTION STATUS'
        );
    }

}
