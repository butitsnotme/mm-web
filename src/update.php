<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('html_errors', 1);
error_reporting(E_ALL);


require_once 'Data.php';
require_once 'Storage.php';

$storage = new Storage("/var/www/localhost/data");

$filter_s = FILTER_DEFAULT;
$flag_s = FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW;
$filter_n = FILTER_VALIDATE_FLOAT;
$flag_n = FILTER_FLAG_ALLOW_THOUSAND;

$schedules = [
    FinanceItem::SEMI_MONTHLY,
    FinanceItem::BI_WEEKLY,
    FinanceItem::MONTHLY
];

$id = filter_input(INPUT_COOKIE, "ID", $filter_s, $flag_s);

$method = filter_input(INPUT_SERVER, "REQUEST_METHOD", $filter_s, $flag_s);
$type = filter_input(INPUT_POST, "type", $filter_s, $flag_s);
$name = filter_input(INPUT_POST, "name", $filter_s, $flag_s);
$value = filter_input(INPUT_POST, "value", $filter_n, $flag_n);
$current = filter_input(INPUT_POST, "current", $filter_n, $flag_n);
$schedule = filter_input(INPUT_POST, "schedule", $filter_n, $flag_n);
$start = filter_input(INPUT_POST, "start", $filter_s, $flag_s);
$end = filter_input(INPUT_POST, "end", $filter_s, $flag_s);

if ("POST" == $method && (in_array($schedule, $schedules) || is_null($schedule))) {
    $data = $storage->load($id);
    
    $start = new DateTimeImmutable($start);
    $end = "" != $end ? new DateTimeImmutable($end) : null;
    
    if ("income" == $type) {
        $i = new IncomeItem($name, $value, $schedule, $start, $end);
        $data->addItem($i);
    }
    if ("expense" == $type) {
        $i = new ExpenseItem($name, $value, $schedule, $start, $end);
        $data->addItem($i);
    }
    if ("target" == $type) {
        $i = new TargetItem($name, $value, $current, $start, $end);
        $data->addTarget($i);
    }
    
    $storage->save($data);
}


header("Location: /", 303);
