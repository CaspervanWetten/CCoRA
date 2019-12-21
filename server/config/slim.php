<?php
/**
 * Configuration for Slim
 *
 * @author Lucas Steehouwer
 */

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

$config['httpVersion']                       = $httpVersion;
$config['responseChunckSize']                = $responseChunckSize;
$config['outputBuffering']                   = $outputBuffering;
$config['determineRouteBeforeAppMiddleware'] = $determineRouteBeforeAppMiddleware;
$config['displayErrorDetails']               = $displayErrorDetails;
$config['addContentLengthHeader']            = $addContentLengthHeader;
