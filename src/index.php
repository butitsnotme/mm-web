<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('html_errors', 1);
error_reporting(E_ALL);

require_once 'Storage.php';
require_once 'Data.php';

$storage = new Storage("/var/www/localhost/data");

$id = filter_input(INPUT_COOKIE, "ID", FILTER_DEFAULT, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);

if (!$id) {
    header("Location: file.php", 303);
    exit(0);
}

$data = $storage->load($id);


setcookie("ID", $data->getName(), time() + 86400 * 3653);

function stringify($rendered) {
    return join("\n", $rendered);
}

?>
<html lang="en">
    <head>
        <meta charset="utf=8">
        <title>MoneyModel Web - View Model</title>
    </head>
    <body>
        <h1>MoneyModel Web - View Model</h1>
        <?php echo stringify($data->render()); ?>
        <p><a href="file.php">Manage Data Files</a>
    </body>
</html>