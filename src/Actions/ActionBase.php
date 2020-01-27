<?php

namespace TForce\Actions;

use TForce\Exceptions\TForceException;

abstract class ActionBase
{

    /**
     * @param string $actionName
     * @throws TForceException
     */
    public function __get(string $actionName)
    {
        throw new TForceException(
            ' You are in ' . self::class . ' .' .
            ' Please extend and create your own Action::class! '
        );
    }

    /**
     * @param string $actionName
     * @param $value
     * @throws TForceException
     */
    public function __set(string $actionName, $value)
    {
        throw new TForceException(
            ' You are in ' . self::class . ' .' .
            ' Please extend and create your own Action::class! '
        );
    }


    abstract function getCommonName();

    abstract function getInnerName();

    /**
     * @param int $curUser_id
     * @param int $customer_id
     * @param int $executor_id
     */
    abstract function isAvailable(int $curUser_id, int $customer_id, int $executor_id);

}
