<?php

// enable use strict types
declare(strict_types=1);

// enable composer autoload
require_once '../vendor/autoload.php';

use TForce\Task;

$taskInstance = new Task(2,3);

echo "<br>" . __FILE__ . " --- " . __LINE__ . "<pre>";
var_dump($taskInstance->getActionsByStatus(Task::STATUS_CANCELED));
echo  "</pre><br>";
