<?php
namespace TForce\Actions;

use TForce\Actions\ActionBase;


class ActionComplete extends ActionBase
{

    private $publicName = 'Завершить';
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
    }
}
