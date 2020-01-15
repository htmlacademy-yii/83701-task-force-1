<?php
namespace TForce\Actions;

use TForce\Actions\ActionBase;


class ActionReject extends ActionBase
{
    private const PUBLIC_NAME = 'Отказаться';
    private const INNER_NAME  = 'act_reject';

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
        return $curUser_id === $executor_id;
    }
}
