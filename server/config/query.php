<?php
/**
 * Database query settings
 *
 * @author Lucas Steehouwer
 */

/**
 * The standard maximum results for resource array requests
 * Can be overriden for specific controllers.
 * @var integer
 */
$maxResultSize = 100;

/**
 * The maximum amount of results for user array requests
 * @var integer
 */
$maxUserResultSize = $maxResultSize;

/**
 * The maximum amount of results for petrinet array requests
 * @var integer
 */
$maxPetrinetResultSize = $maxResultSize;

defined("MAX_RESULT_SIZE")          or define("MAX_RESULT_SIZE",          $maxResultSize);
defined("MAX_USER_RESULT_SIZE")     or define("MAX_USER_RESULT_SIZE",     $maxUserResultSize);
defined("MAX_PETRINET_RESULT_SIZE") or define("MAX_PETRINET_RESULT_SIZE", $maxPetrinetResultSize);
