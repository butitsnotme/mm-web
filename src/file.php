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

function set_cookies_and_redir($id, $ids) {
    if (0 < strlen($id)) {
        if (!in_array($id, $ids)) {
            array_push($ids, $id);
        }
        setcookie("ID", $id, time() + 86400 * 3653);
    } else {
        setcookie("ID", "", 0);
    }
    
    if (0 < count($ids)) {
        setcookie("IDs", join(":", $ids), time() + 86400 * 3653);
    } else {
        setcookie("IDs", "", 0);
    }
    
    
    header("Location: /", 303);
    
    exit(0);
}

$storage = new Storage("/var/www/localhost/data");

$filter = FILTER_DEFAULT;
$flag_lh = FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW;

$id = filter_input(INPUT_COOKIE, "ID", $filter, $flag_lh);
$ids = filter_input(INPUT_COOKIE, "IDs", $filter, $flag_lh);
if (0 < strlen($ids)) {
    $ids = explode(":", $ids);
} else {
    $ids = [];
}

$method = filter_input(INPUT_SERVER, "REQUEST_METHOD", $filter, $flag_lh);
$create = filter_input(INPUT_POST, "CREATE", $filter, $flag_lh);
$switch = filter_input(INPUT_POST, "SWITCH", $filter, $flag_lh);
$delete = filter_input(INPUT_POST, "DELETE", $filter, $flag_lh);



if ("" != $create) {
    $data = Data::new($create);
    $storage->save($data);
    $id = $data->getName();
    set_cookies_and_redir($id, $ids);
}

if ("" != $switch) {
    try {
        $data = $storage->load($switch);
        $id = $switch;
        set_cookies_and_redir($id, $ids);
    } catch (Exception $e) {
        
    }
}

if ("" != $delete) {
    $storage->delete($delete);
    if ($delete == $id) {
        $id = "";
    }
    $ids = array_diff($ids, [ $delete ]);
    set_cookies_and_redir($id, $ids);
}

?>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>MoneyModel Web - Select Data File</title>
        <link rel="stylesheet" href="normalize.css">
        <link rel="stylesheet" href="main.css">
    </head>
    <body>
        <h1>MoneyModel Web - Select Data File</h1>
        <p>Select a data file or create a new data file.
            Data files are where financial models are stored. Please make sure
            you copy the "File ID"s somewhere safe, as these will be needed to
            recover your model if you clear your browser cookies.
        <p>You may recover models not listed here by entering their "File ID"
            under "Add Existing" and selecting "Add". This is the only way to do
            so. If you do not have the "File ID", your model cannot be recovered.
        <p>Please note that the 'X' button next to each data file will delete the
            file completely and irrevocably without confirmation. Do not click it
            unless you really want to get rid of the data file.
        <p>To remove all of your data from the system, simply delete all the listed
            data files.
<?php if (0 < strlen($id)) { ?>
        <p>Current data file: <?php echo $storage->load($id)->getDisplayName(); ?>
<?php } if (0 < count($ids)) { ?>
        <h2>Select Existing Data File</h2>
<?php foreach($ids as $s) { ?>
        <div class="form-container">
        <form method="POST">
            (File ID:
            <input type="text" name="SWITCH" value="<?php echo $s; ?>" readonly="readonly">)
            <input type="submit" name="submit" value="<?php echo $storage->load($s)->getDisplayName(); ?>">
        </form>
        <form method="POST">
            <input type="hidden" name="DELETE" value="<?php echo $s; ?>">
            <input type="submit" name="submit" value="X">
        </form>
        </div>
<?php } } ?>
        <h2>Create New</h2>
        <form method="POST">
            <p>Create a new Data file:
            <input type="text" name="CREATE" placeholder="Name">
            <input type="submit" name="submit" value="Create">
        </form>
        <h2>Add Existing</h2>
        <form method="POST">
            <p>Add an existing Data file:
                <input type="text" name="SWITCH" placeholder="File ID">
                <input type="submit" name="submit" value="Add">
        </form>
    </body>
</html>