<?php

/**
 * Setup for the routes
 *
 * @author Lucas Steehouwer
 */

// Setup for the router groups. The 'api' group forms the root
// group. The other groups are children of this group. This results in
// the following structure:
// API
//     User
//     Petrinet
//     Session

$apiGroup = "api";

$userGroup = "user";

$petrinetGroup = "petrinet";

$sessionGroup = "session";

defined("API_GROUP")      or define("API_GROUP", $apiGroup);
defined("USER_GROUP")     or define("USER_GROUP", $userGroup);
defined("PETRINET_GROUP") or define("PETRINET_GROUP", $petrinetGroup);
defined("SESSION_GROUP")  or define("SESSION_GROUP", $sessionGroup);
