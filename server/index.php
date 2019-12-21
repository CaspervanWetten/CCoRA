<?php

$configFolder = "config";

defined("CONFIG_FOLDER") or define("CONFIG_FOLDER", $configFolder);

$config = array();

include CONFIG_FOLDER . DIRECTORY_SEPARATOR . "folders.php";
include CONFIG_FOLDER . DIRECTORY_SEPARATOR . "database.php";
include CONFIG_FOLDER . DIRECTORY_SEPARATOR . "tables.php";
include CONFIG_FOLDER . DIRECTORY_SEPARATOR . "query.php";
include CONFIG_FOLDER . DIRECTORY_SEPARATOR . "environment.php";
include CONFIG_FOLDER . DIRECTORY_SEPARATOR . "slim.php";
include CONFIG_FOLDER . DIRECTORY_SEPARATOR . "routes.php";

// bootstrap the application
include PUBLIC_FOLDER . DIRECTORY_SEPARATOR . "index.php";
