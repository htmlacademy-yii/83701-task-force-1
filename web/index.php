<?php

// enable use strict types
declare(strict_types = 1);

// enable composer autoload
require_once '../vendor/autoload.php';

use TForce\Logic\Task;


$taskInstance = new Task(2, 3);

$x7 = $taskInstance->getCurStatus();

echo "<br>" . __FILE__ . " --- " . __LINE__ . "<pre>";
var_dump($x7);
echo  "</pre><br>";



