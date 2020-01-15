<?php
namespace TForce\Actions;

use TForce\Actions\ActionBase;


class ActionComplete extends ActionBase
{

    private $publicName = 'Завершить';
    private $innerName = 'act_complete';

    public function getCommonName()
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
