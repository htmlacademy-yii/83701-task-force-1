<?php

namespace TForce;

class Task {

    private const STATUSES_TO_ACTIONS = [
        'NEW'      => [
            'actions' => [
                'cancel'  => 'CANCELED',
                'respond' => 'WORKING'
            ]
        ],
        'CANCELED' => [
            'actions' => []
        ],
        'WORKING'  => [
            'actions' => [
                'setDone' => 'DONE',
                'reject'  => 'FAILED'
            ]
        ],
        'DONE'     => [
            'actions' => []
        ],
        'FAILED'   => [
            'actions' => []
        ],
    ];
    private const USER_ROLES = ['CUSTOMER', 'EXECUTOR'];

    private $curStatus;
    private $executorId;
    private $customerId;
    private $timeEnd;

    public function __construct() {
        $this->curStatus = 'NEW';
    }

    public function getAllStatuses(): array {
        return array_keys(self::STATUSES_TO_ACTIONS);
    }

    public function getAllActions(): array {
        $arrOfActionsPerStatus = array_column(self::STATUSES_TO_ACTIONS, 'actions');
        $arrOfActions = [];

        foreach ($arrOfActionsPerStatus as $index => $actionsPerStatus) {
            $arrOfActions = array_merge(
                $arrOfActions,
                array_keys($actionsPerStatus)
            );
        }

        return $arrOfActions;
    }

    public function getCurStatus(): string {
        return $this->curStatus;
    }

    public function getStatusByAction(string $action): ?string {

        $curStatus = $this->curStatus;
        $availableActions = self::STATUSES_TO_ACTIONS[$curStatus]['actions'];

        if (array_key_exists($action, $availableActions)) {
            return $availableActions[$action];
        }

        return null;


    }


}




