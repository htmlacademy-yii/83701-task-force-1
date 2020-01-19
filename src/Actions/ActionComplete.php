<?php
namespace TForce\Actions;

use TForce\Actions\ActionBase;


class ActionComplete extends ActionBase
{

    private const PUBLIC_NAME = 'Завершить';
    private const INNER_NAME = 'act_complete';

    private static $instance = null;

    public static function getInstance(): ActionComplete
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getCommonName()
    {
        return self::PUBLIC_NAME;
    }

    public function getInnerName()
    {
        return self::INNER_NAME;
    }

    public function isAvailable($curUser_id, $customer_id, $executor_id)
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
