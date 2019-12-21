<?php
/**
 * Database configution
 * 
 * @author Lucas Steehouwer
 */

/**
 * Data Source Name for the database. Typically constructed as follows:
 * dbdriver:dbname=name;host=myhost.com.
 * @var string
 */
$dsn = "mysql:dbname=test;host=localhost";

/**
 * The user for the database
 * @var string
 */
$dbuser = '';

/**
 * The password for the database
 * @var string
 */
$dbpass = '';

/**
 * An array containing extra settings for the database, which is required for
 * some drivers.
 * @var array
 */
$dbsettings = array();

$config['db'] = array(
    'dsn'  => $dsn,
    'user' => $dbuser,
    'pass' => $dbpass,
);
