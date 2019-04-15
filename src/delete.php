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

$id = filter_input(INPUT_COOKIE, "ID", $filter_s, $flag_s);
$name = filter_input(INPUT_GET, "name", $filter_s, $flag_s);
$type = filter_input(INPUT_GET, "type", $filter_s, $flag_s);

if ("" !== $id && "" != $name) {
    $data = $storage->load($id);
    
    if ("target" === $type) {
        $data->delTarget($name);
    } else {
        $data->delItem($name);
    }
    
    $storage->save($data);
}

header("Location: /", 303);