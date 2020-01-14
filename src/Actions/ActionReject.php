<?php
namespace TForce\Actions;

use TForce\Actions\ActionBase;


class ActionReject extends ActionBase
{
    private $publicName = 'Отказаться';
    private $innerName = 'act_reject';

    public function getPublicName()
    {
        return $this->publicName;
    }

    public function getInnerName()
    {
        return $this->innerName;
    }

    public function isAvailable($curUser_id, $customer_id, $executor_id)
    {
        return $curUser_id === $executor_id;
    }
}
