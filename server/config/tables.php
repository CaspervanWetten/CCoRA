<?php
/**
 * Database table settings
 *
 * @author Lucas Steehouwer
 */

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
 * Name of the table containing places of a Petri net
 * @var string
 */
$petrinetPlaceTable = "petrinet_place";

/**
 * Name of the table containing transitions of a Petri net
 * @var string
 */
$petrinetTransitionTable = "petrinet_transition";

/**
 * Name of the table containing place -> transition flows of a
 * particular Petri net
 * @var string
 */
$petrinetFlowPlaceTransitionTable = "petrinet_flow_pt";

/**
 * Name of the table containing transition -> place flows of a
 * particular Petri net
 * @var string
 */
$petrinetFlowTransitionPlaceTable = "petrinet_flow_tp";

/**
 * Name of the table containing all the markings for a Petri net
 * @var string
 */
$petrinetMarkingTable = "petrinet_marking";

/**
 * Name of the table containing marking pairs within a petrinet
 * Marking pairs: (place, tokens)
 * @var string
 */
$petrinetMarkingPairTable = "petrinet_marking_pair";


defined("USER_TABLE") or define("USER_TABLE", $userTable);

defined("PETRINET_TABLE")              or define("PETRINET_TABLE",              $petrinetTable);
defined("PETRINET_PLACE_TABLE")        or define("PETRINET_PLACE_TABLE",        $petrinetPlaceTable);
defined("PETRINET_TRANSITION_TABLE")   or define("PETRINET_TRANSITION_TABLE",   $petrinetTransitionTable);
defined("PETRINET_FLOW_PT_TABLE")      or define("PETRINET_FLOW_PT_TABLE",      $petrinetFlowPlaceTransitionTable);
defined("PETRINET_FLOW_TP_TABLE")      or define("PETRINET_FLOW_TP_TABLE",      $petrinetFlowTransitionPlaceTable);
defined("PETRINET_MARKING_TABLE")      or define("PETRINET_MARKING_TABLE",      $petrinetMarkingTable);
defined("PETRINET_MARKING_PAIR_TABLE") or define("PETRINET_MARKING_PAIR_TABLE", $petrinetMarkingPairTable);
