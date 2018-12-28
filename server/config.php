<?php
/**
 * @author Lucas Steehouwer
 *
 * This file contains configuration settings for the project and Slim.
 * Keep the settings in this file secret.
 */

 /**********************************************************
  *                      COZP SETTINGS                     *
  *********************************************************/

//   FOLDER SETTINGS
/**
 * Path to the folder wherein the routes are defined
 * @var string
 */
$publicFolder = "public";

/**
 * Path to the vendor folder
 * @var string
 */
$applicationFolder = "application";

/**
 * Path to the folder wherein the user information is stored temporarily
 * @var string
 */
$userFolder = "application". DIRECTORY_SEPARATOR . "user";

/**
 * Path the folder wherein the logs for each user is stored
 */
$logFolder = "application" . DIRECTORY_SEPARATOR . "log";

defined("PUBLIC_FOLDER") or  define('PUBLIC_FOLDER', $publicFolder);
defined("APPLICATION_FOLDER") or define('APPLICATION_FOLDER', $applicationFolder);
defined("VENDOR_FOLDER") or define('VENDOR_FOLDER', $applicationFolder . DIRECTORY_SEPARATOR . 'vendor');
defined("USER_FOLDER") or define('USER_FOLDER', $userFolder);
defined("LOG_FOLDER") or define("LOG_FOLDER", $logFolder);

// EXTERNAL PROGRAM SETTINGS
/**
 * Path to Graphviz. If the program is available globally just "dot" is enough.
 * @var string
 */
$dotPath = "dot";

define("DOT_PATH", $dotPath);

//  DATABASE SETTINGS
/**
 * Name of the user table
 * @var string
 */
$userTable = "user";

/**
 * Name of the main petrinet table
 * @var string
 */
$petrinetTable = "petrinet";

/**
 * Name of the table containing invidual petrinet elements
 * @var string
 */
$petrinetElementTable = "petrinet_element";

/**
 * Name of the table containing flows within a petrinet
 * @var string
 */
$petrinetFlowTable = "petrinet_flow";

/**
 * Name of the table containing marking pairs within a petrinet
 * Marking pairs: (place, tokens)
 * @var string
 */
$petrinetMarkingPairTable = "petrinet_marking_pair";


defined("USER_TABLE") or define("USER_TABLE", $userTable);

defined("PETRINET_TABLE") or define("PETRINET_TABLE", $petrinetTable);
defined("PETRINET_ELEMENT_TABLE") or define("PETRINET_ELEMENT_TABLE", $petrinetElementTable);
defined("PETRINET_FLOW_TABLE") or define("PETRINET_FLOW_TABLE", $petrinetFlowTable);
defined("PETRINET_MARKING_PAIR_TABLE") or define("PETRINET_MARKING_PAIR_TABLE", $petrinetMarkingPairTable);

/**********************************************************
 *                      SLIM SETTINGS                     *
 *********************************************************/

/**
 * The protocol version used by the Slim\Response object
 * @var string
 */
$httpVersion = '1.1';

/**
 * Size of each chunk read from the Response Body.
 * @var integer
 */
$responseChunckSize = 4096;

/**
 * Describes where echo and print statements are put in the response body.
 * False for no buffering. 'append' for at the end, 'prepend' for at the front.
 * @var boolean|string.
 */
$outputBuffering = 'prepend';

/**
 * When true, the route is calculated before any middleware is executed.
 * This means that you can inspect route parameters in middleware if you need to.
 * @var boolean
 */
$determineRouteBeforeAppMiddleware = FALSE;

/**
 * Display the details of an error. True for development, false for production
 * @var boolean
 */
$displayErrorDetails = TRUE;

/**
 * Whether to add the content length header
 * @var boolean
 */
$addContentLengthHeader = FALSE;

/**
 * Filename for caching the FastRoute routes. Must be set to to a valid filename
 * within a writeable directory. If the file does not exist, then it is created
 * with the correct cache information on first run. Set to false to disable the
 * FastRoute cache system.
 * @var boolean|string
 */

$routerCacheFile = FALSE;

$config = array(
    'httpVersion'                       => $httpVersion,
    'responseChunckSize'                => $responseChunckSize,
    'outputBuffering'                   => $outputBuffering,
    'determineRouteBeforeAppMiddleware' => $determineRouteBeforeAppMiddleware,
    'displayErrorDetails'               => $displayErrorDetails,
    'addContentLengthHeader'            => $addContentLengthHeader,
);

/**********************************************************
 *                   DATABASE SETTINGS                    *
 *********************************************************/

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
    'dsn'   => $dsn,
    'user'  => $dbuser,
    'pass'  => $dbpass,
);

require_once PUBLIC_FOLDER . '/index.php';
?>
