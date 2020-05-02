<?php
/**
 * Cross Origin Resource Sharing (CORS) settings
 *
 * @author Lucas Steehouwer
 */


/**
 * Whether to enable CORS or not.
 * If you have enabled CORS in your server's software, you do not need
 * to enable it here as well
 * default: false
 * @var bool
 */
$enableCors = false;

/**
 * Which origins should be allowed by the CORS.
 * default: "*"
 * @var string
 */
$allowCors = "*";

defined("CORS_ENABLED") or define("CORS_ENABLED", $enableCors);
defined("CORS_ALLOW") or   define("CORS_ALLOW", $allowCors);
