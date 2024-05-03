<?php
$version                                            = "3.6.80";
$date                                               = "2024-05-03";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["features"][]      = "Add support for multiple conditions in latest events custom format string e.g. a!m for events that are all day but NOT multi-day events";
$changelog[$extension][$version]["features"][]      = "";
$changelog[$extension][$version]["features"][]      = "";

$changelog[$extension][$version]["bugfixes"]        = array();
$changelog[$extension][$version]["bugfixes"][]      = "Make sure TITLE_LINK respecte module target menu item settings";
$changelog[$extension][$version]["bugfixes"][]      = "If 'none' editor isn't available fall back to codemirror";
$changelog[$extension][$version]["bugfixes"][]      = "Fix for UNTIL date exporting for RSVP Pro iCal invites";
$changelog[$extension][$version]["bugfixes"][]      = "Fix for JevDate class declaration to maintain compatibility for Joomla 3 - 5 and PHP 7.4 - 8.2";
$changelog[$extension][$version]["bugfixes"][]      = "Make sure gwejson plugin doesn't require Joomla 5 compatibility plugin";
$changelog[$extension][$version]["bugfixes"][]      = "Fix for router from plugin where the target menu item is icalrepeat.detail - we need to make sure the link is to the actual event and not the menu event!";
$changelog[$extension][$version]["bugfixes"][]      = "";
$changelog[$extension][$version]["bugfixes"][]      = "";
$changelog[$extension][$version]["bugfixes"][]      = "";
