<?php
$version                                            = "3.6.42";
$date                                               = "2022-09-30";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["bugfixes"]        = array();

$changelog[$extension][$version]["features"][]      = "Enhance MS outlook export links";
$changelog[$extension][$version]["features"][]      = "Make enhanced calendar export layouts the default";
$changelog[$extension][$version]["features"][]      = "Improve latest events module query - subquery was not being optimised by MySQL";
$changelog[$extension][$version]["features"][]      = "New config option for disabling creating new categories when importing iCal calendars";
$changelog[$extension][$version]["features"][]      = "Customised layouts now support field replacements in custom CSS";
$changelog[$extension][$version]["features"][]      = "Date range menu item when used on a single date menu item e.g. for float theme no longer needs range_startdate and range_enddate to navigate";

$changelog[$extension][$version]["bugfixes"][]      = "Make sure ; is added to the end of addEventListener function calls - was causing problems for some javascript concatenation scripts";
$changelog[$extension][$version]["bugfixes"][]      = "Fix for cassiopeia template frontend header disappearing when editing events";
$changelog[$extension][$version]["bugfixes"][]      = "Fix for list checking for action buttons in backend";
$changelog[$extension][$version]["bugfixes"][]      = "Make dtstart a bigint to support historic events more than 100 years in the past";
$changelog[$extension][$version]["bugfixes"][]      = "Fix for static declaration if iCalRRuleFromDB";
$changelog[$extension][$version]["bugfixes"][]      = "Fix for modal library needed to support RSVP Pro ticket links";
$changelog[$extension][$version]["bugfixes"][]      = "Fix for typeahead library which breaks if the number of returned values matched the max to be displayed!";
