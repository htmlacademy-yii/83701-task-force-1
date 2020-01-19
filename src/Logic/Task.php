<?php
namespace TForce\Logic;

use TForce\Actions\{
    ActionBase, ActionCancel, ActionComplete, ActionReject, ActionRespond
};
use TForce\Exceptions\TForceException;


/**
 * Class Task
 * @package TForce\Logic
 */
class Task
{
    const STATUSES = [
        self::STATUS_NEW      => 'новое',
        self::STATUS_CANCELED => 'отменено',
        self::STATUS_WORKING  => 'в работе',
        self::STATUS_DONE     => 'выполнено',
        self::STATUS_FAILED   => 'провалено'
    ];
    const STATUS_NEW = 'new';
    const STATUS_CANCELED = 'canceled';
    const STATUS_WORKING = 'working';
    const STATUS_DONE = 'done';
    const STATUS_FAILED = 'failed';
    const ROLE_CUSTOMER = 'customer';
    const ROLE_EXECUTOR = 'executor';
    const ROLES = [
        self::ROLE_EXECUTOR => 'исполнитель',
        self::ROLE_CUSTOMER => 'заказчик'
    ];


    static private $actionCancel;
    static private $actionRespond;
    static private $actionComplete;
    static private $actionReject;

    static $MAP_STATUS_ACTION;
    static $MAP_ACTION_STATUS;

    private $curStatus;
    private $executorId;
    private $customerId;
    private $timeEnd;
    private $actionObjects;

    /**
     * Task constructor.
     * @param int $executorId
     * @param int $customerId
     * @param string|null $status
     */
    public function __construct(int $customerId, int $executorId, string $status = null)
    {

        if ($customerId === $executorId) {
            throw new TForceException(
                'ID of customer must not be equal ID of executor'
            );
        }

        $status = $status ?? self::STATUS_NEW;

        if (!array_key_exists($status, self::STATUSES)) {
            throw new TForceException(
                'Available status is only one from: ' .
                implode(', ', array_keys(self::STATUSES))
            );
        }

        $this->curStatus = $status;
        $this->executorId = $executorId;
        $this->customerId = $customerId;


        self::$actionCancel = ActionCancel::getInstance();
        self::$actionRespond = ActionRespond::getInstance();
        self::$actionComplete = ActionComplete::getInstance();
        self::$actionReject = ActionReject::getInstance();

        $this->actionObjects = [
            (self::$actionCancel)->getInnerName()   => self::$actionCancel,
            (self::$actionRespond)->getInnerName()  => self::$actionRespond,
            (self::$actionComplete)->getInnerName() => self::$actionComplete,
            (self::$actionReject)->getInnerName()   => self::$actionReject
        ];

        self::$MAP_STATUS_ACTION = [
            self::STATUS_NEW      => [self::$actionCancel, self::$actionRespond],
            self::STATUS_CANCELED => [],
            self::STATUS_WORKING  => [self::$actionComplete, self::$actionReject],
            self::STATUS_DONE     => [],
            self::STATUS_FAILED   => []
        ];

        self::$MAP_ACTION_STATUS = [
            (self::$actionCancel)->getInnerName()   => self::STATUS_CANCELED,
            (self::$actionRespond)->getInnerName()  => self::STATUS_WORKING,
            (self::$actionComplete)->getInnerName() => self::STATUS_DONE,
            (self::$actionReject)->getInnerName()   => self::STATUS_FAILED
        ];

    }

    /**
     * @return array MapStatusAction
     */
    public function getMapStatusAction(): array
    {
        return self::$MAP_STATUS_ACTION;
    }

    /**
     * @return array MapActionStatus
     */
    public function getMapActionStatus(): array
    {
        return self::$MAP_ACTION_STATUS;
    }

    /**
     * @return array All Statuses
     */
    public function getAllStatuses(): array
    {
        return self::STATUSES;
    }

    /**
     * @return array All Actions
     */
    public function getAllActions(): array
    {
        return $this->actionObjects;
    }

    /**
     * @return string Current status of Task
     */
    public function getCurStatus(): string
    {
        return $this->curStatus;
    }

    /**
     * @param string $action
     * @return string Status after Action
     */
    public function getStatusAfterAction(ActionBase $action): string
    {
        return self::$MAP_ACTION_STATUS[$action->getInnerName()];
    }

    /**
     * @param string $status
     * @return array All actions for corresponding status
     */
    public function getActionsByStatus(int $curUser_id, string $status): array
    {
        if (!array_key_exists($status, self::STATUSES)) {
            throw new TForceException(
                'Available status is only one from: ' .
                implode(', ', array_keys(self::STATUSES))
            );
        }

        $objActions = self::$MAP_STATUS_ACTION[$status];

        return array_filter(
            $objActions,
            function ($oneObjAction) use ($curUser_id) {
                return $oneObjAction->isAvailable(
                    $curUser_id,
                    $this->customerId,
                    $this->executorId
                );
            }
        );

    }

}




