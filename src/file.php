<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('html_errors', 1);
error_reporting(E_ALL);


require_once 'Data.php';
require_once 'Storage.php';

$storage = new Storage("/var/www/localhost/data");

$filter = FILTER_DEFAULT;
$flag_lh = FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW;

$id = filter_input(INPUT_COOKIE, "ID", $filter, $flag_lh);
$ids = filter_input(INPUT_COOKIE, "IDs", $filter, $flag_lh);
$ids = explode(":", $ids);

$method = filter_input(INPUT_SERVER, "REQUEST_METHOD", $filter, $flag_lh);
$create = filter_input(INPUT_POST, "CREATE", $filter, $flag_lh);
$switch = filter_input(INPUT_POST, "SWITCH", $filter, $flag_lh);

if ("" != $id) {
    if (!in_array($id, $ids)) {
        array_push($ids, $id);
    }
    setcookie("ID", $id, time() + 86400 * 3653);
    setcookie("IDs", join(":", $ids), time() + 86400 * 3653);
}

if ("" != $create) {
    $data = Data::new();
    $storage->save($data);
    setcookie("ID", $data->getName(), time() + 86400 * 3653);
    header("Location: /", 303);
    exit(0);
}

if ("" != $switch) {
    try {
        $data = $storage->load($switch);
        setcookie("ID", $data->getName(), time() + 86400 * 3653);
        header("Location: /", 303);
        exit(0);
    } catch (Exception $e) {
        
    }
}

?>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>MoneyModel Web - Select Data File</title>
    </head>
    <body>
        <h1>MoneyModel Web - Select Data File</h1>
        <p>Select a data file or create a new data file.
<?php if ("" !== $id) { ?>
        <p>Current data file: <?php echo $id; ?>
<?php } if (0 < count($ids)) { ?>
        <h2>Select Existing Data File</h2>
<?php foreach($ids as $s) { ?>
        <form method="POST"><input type="submit" name="SWITCH" value="<?php echo $s; ?>"></form>
<?php } } ?>
        <h2>Create New</h2>
        <form method="POST">
            <p>Create a new Data file:
            <input type="submit" name="CREATE" value="Create">
        </form>
    </body>
</html>