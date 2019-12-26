<?php
namespace TForce;

/**
 * Class Task
 * @package TForce
 */
class Task {

    private const STATUS_NEW = 'new';
    private const STATUS_CANCELED = 'canceled';
    private const STATUS_WORKING = 'working';
    private const STATUS_DONE = 'done';
    private const STATUS_FAILED = 'failed';

    private const STATUSES = [
        self::STATUS_NEW      => 'новое',
        self::STATUS_CANCELED => 'отменено',
        self::STATUS_WORKING  => 'в работе',
        self::STATUS_DONE     => 'выполнено',
        self::STATUS_FAILED   => 'провалено',
    ];

    private const ACTION_CANCEL = 'cancel';
    private const ACTION_RESPOND = 'respond';
    private const ACTION_COMPLETE = 'complete';
    private const ACTION_REJECT = 'reject';

    private const ACTIONS = [
        self::ACTION_CANCEL   => 'отменить',
        self::ACTION_RESPOND  => 'откликнуться',
        self::ACTION_COMPLETE => 'завершить',
        self::ACTION_REJECT   => 'отказаться'
    ];

    private const MAP_STATUS_ACTION = [
        self::STATUS_NEW      => [self::ACTION_CANCEL, self:: ACTION_RESPOND],
        self::STATUS_CANCELED => [],
        self::STATUS_WORKING  => [self::ACTION_COMPLETE, self::ACTION_REJECT],
        self::STATUS_DONE     => [],
        self::STATUS_FAILED   => []
    ];
    private const MAP_ACTION_STATUS = [
        self::ACTION_CANCEL   => self::STATUS_CANCELED,
        self::ACTION_RESPOND  => self::STATUS_WORKING,
        self::ACTION_COMPLETE => self::STATUS_DONE,
        self::ACTION_REJECT   => self::STATUS_FAILED
    ];

    private const ROLE_CUSTOMER = 'customer';
    private const ROLE_EXECUTOR = 'executor';

    private const ROLES = [
        self::ROLE_EXECUTOR => 'исполнитель',
        self::ROLE_CUSTOMER => 'заказчик'
    ];

    private $curStatus;
    private $executorId;
    private $customerId;
    private $timeEnd;

    /**
     * Task constructor.
     * @param int $executorId
     * @param int $customerId
     */
    public function __construct(int $executorId, int $customerId) {
        $this->executorId = $executorId;
        $this->customerId = $customerId;
        $this->curStatus = self::STATUS_NEW;
    }

    /**
     * @return array MapStatusAction
     */
    public function getMapStatusAction(): array {
        return self::MAP_STATUS_ACTION;
    }

    /**
     * @return array MapActionStatus
     */
    public function getMapActionStatus(): array {
        return self::MAP_ACTION_STATUS;
    }

    /**
     * @return array All Statuses
     */
    public function getAllStatuses(): array {
        return self::STATUSES;
    }

    /**
     * @return array All Actions
     */
    public function getAllActions(): array {
        return self::ACTIONS;
    }

    /**
     * @return string Current status of Task
     */
    public function getCurStatus(): string {
        return $this->curStatus;
    }

    /**
     * @param string $action
     * @return string Status after Action
     */
    public function getStatusAfterAction(string $action): string {
        return self::MAP_ACTION_STATUS[$action];
    }

    /**
     * @param string $status
     * @return array All actions for corresponding status
     */
    public function getActionsByStatus(string $status): array {
        return self::MAP_STATUS_ACTION[$status];
    }

}




