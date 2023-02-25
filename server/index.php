<?php

/** load dependencies **/
require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$env = Dotenv\Dotenv::createImmutable(__DIR__);
$env->load();

// bootstrap the application
include 'public' . DIRECTORY_SEPARATOR . 'index.php';
