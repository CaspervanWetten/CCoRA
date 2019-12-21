<?php
/**
 * Folder settings
 * 
 * @author Lucas Steehouwerr
 */

/**
 * Path to the folder wherein the routes are defined
 * @var string
 */
$publicFolder = "public";

/**
 * Path to the source code folder
 * @var string
 */
$sourceFolder = "src";

/**
 * Path to the vendor (made by composer) folder
 * @var string
 */
$vendorFolder = "vendor";

/**
 * Path to the folder wherein the user information is stored temporarily
 * @var string
 */
$userFolder = "user";

/**
 * Path the folder wherein the logs for each user is stored
 * @var string
 */
$logFolder = "log";

defined("PUBLIC_FOLDER") or define('PUBLIC_FOLDER', $publicFolder);
defined("SOURCE_FOLDER") or define('SOURCE_FOLDER', $sourceFolder);
defined("VENDOR_FOLDER") or define('VENDOR_FOLDER', $vendorFolder);
defined("USER_FOLDER")   or define('USER_FOLDER',   $userFolder);
defined("LOG_FOLDER")    or define("LOG_FOLDER",    $logFolder);
