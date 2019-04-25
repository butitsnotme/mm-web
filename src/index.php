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
        <title>MoneyModel Web - View/Edit Model - <?php echo $data->getDisplayName(); ?></title>
        <link rel="stylesheet" href="normalize.css">
        <link rel="stylesheet" href="main.css">
    </head>
    <body>
        <h1>MoneyModel Web - View/Edit Model - <?php echo $data->getDisplayName(); ?></h1>
        <p>MoneyModel is a small financial calculator. Give it details on income,
            expenses, and desired savings and it can help you plan.
        <p>Enter your data below. To update a piece of data, simply create a new
            one with the same name, it will overwrite the existing. When entering
            a saving target with some already saved, enter the start date as when
            the current value was current. If you already have some aside, but 
            do not wish to save in earnest yet, enter the amount put aside, and 
            the date you would like to start saving in earnest.
        <p>The "Future Projection" section below shows what your financial situation
            might look like for the time period that you are trying to save.
        <?php echo stringify($data->render()); ?>
        <p><a href="file.php">Manage Data Files</a>
    </body>
</html>