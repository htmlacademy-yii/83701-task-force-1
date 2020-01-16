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


    static $ACTION_CANCEL;
    static $ACTION_RESPOND;
    static $ACTION_COMPLETE;
    static $ACTION_REJECT;

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


        self::$ACTION_CANCEL = new ActionCancel();
        self::$ACTION_RESPOND = new ActionRespond();
        self::$ACTION_COMPLETE = new ActionComplete();
        self::$ACTION_REJECT = new ActionReject();

        $this->actionObjects = [
            (self::$ACTION_CANCEL)->getInnerName()   => self::$ACTION_CANCEL,
            (self::$ACTION_RESPOND)->getInnerName()  => self::$ACTION_RESPOND,
            (self::$ACTION_COMPLETE)->getInnerName() => self::$ACTION_COMPLETE,
            (self::$ACTION_REJECT)->getInnerName()   => self::$ACTION_REJECT
        ];

        self::$MAP_STATUS_ACTION = [
            self::STATUS_NEW      => [self::$ACTION_CANCEL, self::$ACTION_RESPOND],
            self::STATUS_CANCELED => [],
            self::STATUS_WORKING  => [self::$ACTION_COMPLETE, self::$ACTION_REJECT],
            self::STATUS_DONE     => [],
            self::STATUS_FAILED   => []
        ];

        self::$MAP_ACTION_STATUS = [
            (self::$ACTION_CANCEL)->getInnerName()   => self::STATUS_CANCELED,
            (self::$ACTION_RESPOND)->getInnerName()  => self::STATUS_WORKING,
            (self::$ACTION_COMPLETE)->getInnerName() => self::STATUS_DONE,
            (self::$ACTION_REJECT)->getInnerName()   => self::STATUS_FAILED
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




