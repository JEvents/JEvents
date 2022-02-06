<?php
$version = "3.6.25";
$date = "2022-01-21";
$changelog["package_jevents"][$version] = array();
$changelog["package_jevents"][$version]["date"] = $date;
$changelog["package_jevents"][$version]["features"] = array();
$changelog["package_jevents"][$version]["features"][]="Add Joomla 4 JEvents dashboard module - first cut";

$changelog["package_jevents"][$version]["bugfixes"] = array();
$changelog["package_jevents"][$version]["bugfixes"][]="Harden search query in backend list of repeats";
$changelog["package_jevents"][$version]["bugfixes"][]="correct colours of icons for options in authorised user overview page";
$changelog["package_jevents"][$version]["bugfixes"][]="PHP 8 fix for undefined constants";
$changelog["package_jevents"][$version]["bugfixes"][]="Fix translation saving error on J3 with Action Log enable.";
$changelog["package_jevents"][$version]["bugfixes"][]="BS5 doesn't load jquery by default so must explicitly load it";
$changelog["package_jevents"][$version]["bugfixes"][]="Correct layout id in layouts overview";
$changelog["package_jevents"][$version]["bugfixes"][]="Improved styling of event & repeat lists in backend";
$changelog["package_jevents"][$version]["bugfixes"][]="Traps for jQuery not being loaded rather than throwing jQuery undefined errors";
$changelog["package_jevents"][$version]["bugfixes"][]="Layout editing when only one category was defined was defaulting to specific category rather than all categories which could cause problems when new categories were added later";