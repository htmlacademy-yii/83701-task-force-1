<?php

// enable use strict types
declare(strict_types = 1);

// enable composer autoload
require_once '../vendor/autoload.php';

use TForce\Logic\Task;
use TForce\Actions\ActionCancel;
use TForce\CsvToSql\ConverterCsvToSql;

/** TODO Check Task.php functionality
$taskInstance = new Task(2, 3);

$x1 = $taskInstance->getMapStatusAction();
$x2 = $taskInstance->getMapActionStatus();
$x3 = $taskInstance->getAllStatuses();
$x4 = $taskInstance->getAllActions();
$x5 = $taskInstance->getCurStatus();
$x6 = $taskInstance->getStatusAfterAction(ActionCancel::getInstance());
$x7 = $taskInstance->getActionsByStatus(3,Task::STATUS_WORKING);

echo "<br>" . __FILE__ . " --- " . __LINE__ . "<pre>";
var_dump($x7);
echo  "</pre><br>";
*/

/* TODO Check Parsing CSV files */
$converter = new ConverterCsvToSql();
$converter->handleDirWithCSVFiles();
