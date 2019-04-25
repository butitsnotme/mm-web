<?php

/**
 * mm-web
 * Copyright (C) 2019  Dennis Bellinger
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

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