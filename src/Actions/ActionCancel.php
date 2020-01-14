<?php

namespace TForce\Actions;

use TForce\Actions\ActionBase;


class ActionCancel extends ActionBase
{
    private $publicName = 'Отменить';
    private $innerName = 'act_complete';

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
        return $curUser_id === $customer_id;

    }
}
