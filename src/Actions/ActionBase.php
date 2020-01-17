<?php

namespace TForce\Actions;

use TForce\Exceptions\TForceException;

abstract class ActionBase
{

    public function __get($actionName)
    {
        throw new TForceException(
            ' You are in ' . self::class . ' .'.
            ' Please extend and create your own Action::class! '
        );
    }

    public function __set($actionName, $value)
    {
        throw new TForceException(
            ' You are in ' . self::class . ' .'.
            ' Please extend and create your own Action::class! '
        );
    }

    abstract function getCommonName();

    abstract function getInnerName();

    abstract function isAvailable($curUser_id, $customer_id, $executor_id);

}
