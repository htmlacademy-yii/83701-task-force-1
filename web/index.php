<?php

// enable use strict types
declare(strict_types=1);

// enable composer autoload
require_once '../vendor/autoload.php';

use TForce\Task;

$taskInstance = new Task();

echo "<br>" . __FILE__ . " --- " . __LINE__ . "<pre>";
var_dump($taskInstance->getStatusByAction('respond'));
echo  "</pre>";
