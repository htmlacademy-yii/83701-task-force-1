<?php

namespace TForce\Actions;

use TForce\Actions\ActionBase;


class ActionCancel extends ActionBase
{
    private const PUBLIC_NAME = 'Отменить';
    private const INNER_NAME = 'act_cancel';
    private static $instance = null;

    /**
     * @return ActionCancel
     */
    public static function getInstance(): ActionCancel
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return string
     */
    public function getCommonName(): string
    {
        return self::PUBLIC_NAME;
    }

    /**
     * @return string
     */
    public function getInnerName(): string
    {
        return self::INNER_NAME;
    }

    /**
     * @param int $curUser_id
     * @param int $customer_id
     * @param int $executor_id
     * @return bool
     */
    public function isAvailable(
        int $curUser_id,
        int $customer_id,
        int $executor_id
    ): bool
    {
        return $curUser_id === $customer_id;

    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

}
