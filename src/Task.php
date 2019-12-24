<?php


namespace TForce;

/*
TODO +
в виде констант в классе должны быть перечислены все возможные
действия и статусы. Константа определяет внутреннее имя для статуса/действия.

TODO +
во внутренних свойствах класс хранит id исполнителя и id заказчика.
Эти  значения класс получает в своём конструкторе

TODO +
класс имеет методы для возврата «карты» статусов и действий.
Карта — это ассоциативный массив, где ключ — внутреннее имя, а значение —
названия статуса/действия на русском.

TODO +
класс имеет метод для получения статуса, в которой он перейдёт
после выполнения указанного действия

TODO +
класс имеет метод для получения доступных действий для указанного статуса

*/

use PHPUnit\Framework\MockObject\Exception;

class Task {

    public const STATUS_NEW = ['EN' => 'new', 'RU' => 'новое'];
    public const STATUS_CANCELED = ['EN' => 'canceled', 'RU' => 'отменено'];
    public const STATUS_WORKING = ['EN' => 'working', 'RU' => 'в работе'];
    public const STATUS_DONE = ['EN' => 'done', 'RU' => 'выполнено'];
    public const STATUS_FAILED = ['EN' => 'failed', 'RU' => 'провалено'];

    public const ACTION_CANCEL = ['EN' => 'cancel', 'RU' => 'отменить'];
    public const ACTION_RESPOND = ['EN' => 'respond', 'RU' => 'откликнуться'];
    public const ACTION_COMPLETE = ['EN' => 'complete', 'RU' => 'завершить'];
    public const ACTION_REJECT = ['EN' => 'reject', 'RU' => 'отказаться'];

    private const MAP_STATUS_ACTION = [
        self::STATUS_NEW['EN'] => [
            'actions' => [
                self::ACTION_CANCEL['EN']  => self::STATUS_CANCELED['EN'],
                self::ACTION_RESPOND['EN'] => self::STATUS_WORKING['EN']
            ]
        ],

        self::STATUS_WORKING['EN'] => [
            'actions' => [
                self::ACTION_COMPLETE['EN'] => self::STATUS_DONE['EN'],
                self::ACTION_REJECT['EN']   => self::STATUS_FAILED['EN']
            ]
        ],

        self::STATUS_CANCELED['EN'] => [
            'actions' => []
        ],

        self::STATUS_DONE['EN'] => [
            'actions' => []
        ],

        self::STATUS_FAILED['EN'] => [
            'actions' => []
        ]
    ];

    public const ROLE_CUSTOMER = ['EN' => 'customer', 'RU' => 'заказчик'];
    public const ROLE_EXECUTOR = ['EN' => 'executor', 'RU' => 'исполнитель'];

    private $allStatuses;
    private $allActions;
    private $mapStatusAction;
    private $curStatus;
    private $executorId;
    private $customerId;
    private $timeEnd;

    protected static function filterClassConstants(
        $classConstants,
        $filterPrefix
    ) {
        return array_filter(
            $classConstants,
            function ($constName) use ($filterPrefix) {
                $constPrefix = explode('_', $constName)[0];
                return ($constPrefix === $filterPrefix) ? true : false;
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    protected static function getClassConstants($className) {

        $allConstants = (new \ReflectionClass(self::class))->getConstants();

        return [
            'statuses'        =>
                self::filterClassConstants($allConstants, 'STATUS'),
            'actions'         =>
                self::filterClassConstants($allConstants, 'ACTION'),
            'mapStatusAction' =>
                self::filterClassConstants($allConstants, 'MAP')
        ];
    }

    public function __construct(int $executorId, int $customerId) {
        $this->executorId = $executorId;
        $this->customerId = $customerId;
        $this->curStatus = self::STATUS_NEW;
    }

    public function getMapStatusAction(): array {
        if ($this->mapStatusAction === null) {
            $this->mapStatusAction =
                self::getClassConstants(self::class)['mapStatusAction'];
        }
        return $this->mapStatusAction;
    }

    public function getAllStatuses(): array {
        if ($this->allStatuses === null) {
            $this->allStatuses =
                self::getClassConstants(self::class)['statuses'];
        }
        return $this->allStatuses;
    }

    public function getAllActions(): array {
        if ($this->allActions === null) {
            $this->allActions =
                self::getClassConstants(self::class)['actions'];
        }
        return $this->allActions;
    }

    public function getCurStatus(): array {
        return $this->curStatus;
    }

    public function getStatusAfterAction(array $action): ?array {
        $actionStrEn = $action['EN'];
        $curStatusStrEn = $this->curStatus['EN'];
        $actionsByStatus = self::MAP_STATUS_ACTION[$curStatusStrEn]['actions'];

        if (!array_key_exists($actionStrEn, $actionsByStatus)) return null;

        $newStatusStr = $actionsByStatus[$actionStrEn];
        $allStatuses = array_values(self::getAllStatuses());

        $ordinalFilteredStatus = array_filter(
            $allStatuses,
            function ($oneStatus) use ($newStatusStr) {
                return $oneStatus['EN'] === $newStatusStr;
            }
        );

        return (count($ordinalFilteredStatus) !== 0)
            ? current($ordinalFilteredStatus)
            : null;

    }

    public function getActionsByStatus(array $status): ?array {

        $statusStrEn = $status['EN'];
        $actionsByStatusStr =
            array_keys(self::MAP_STATUS_ACTION[$statusStrEn]['actions']);

        $allActions = $this->getAllActions();

        if (count($actionsByStatusStr) === 0) return null;

        $actionsByStatus = array_reduce(
            $actionsByStatusStr,

            function ($curry, $oneActionByStatusStr) use ($allActions) {
                $oneActionArr = array_filter(

                    $allActions,
                    function ($oneAction) use ($oneActionByStatusStr) {
                        return $oneAction['EN'] === $oneActionByStatusStr;
                    }

                );
                array_push($curry, current($oneActionArr));
                return $curry;
            },

            array()
        );

        return $actionsByStatus;

    }

//    public function setNewStatus(array $newStatus) {
//        $allStatuses = array_values($this->getAllStatuses());
//        $isRightNewStatus = in_array($newStatus, $allStatuses);
//        if ($isRightNewStatus) {
//            $this->curStatus = $newStatus;
//            return true;
//        }
//        throw new \Exception('You set up unregistered status');
//    }

}




