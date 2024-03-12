<?php
$version                                            = "3.6.79";
$date                                               = "2023-03-12";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["features"][]      = "Add support for calendar plus navigation with enhanced date output";
$changelog[$extension][$version]["bugfixes"][]      = "Allow google calendar direct import to pick up HTML details";

$changelog[$extension][$version]["bugfixes"]        = array();
$changelog[$extension][$version]["bugfixes"][]      = "Fix for showon handling when using customised event editing pages";
$changelog[$extension][$version]["bugfixes"][]      = "Date picker fix for Joomla 5 dark mode";
$changelog[$extension][$version]["bugfixes"][]      = "Time picker fix for Joomla 5 dark mode";
$changelog[$extension][$version]["bugfixes"][]      = "Make link in iCal export an absolute URL";
$changelog[$extension][$version]["bugfixes"][]      = "Language fix for structured data requirement for managed people";
$changelog[$extension][$version]["bugfixes"][]      = "LDJSON is now output into the head as opposed to the body - was being removed by some 3rd party system plugins";
$changelog[$extension][$version]["bugfixes"][]      = "catch problem with finder cli methods in finder plugin and filters - code was assuming use in web interface only";
$changelog[$extension][$version]["bugfixes"][]      = "Category name is now translatable without the need for JEV_ in the name - just capitalised and translation existing is enough";
$changelog[$extension][$version]["bugfixes"][]      = "Fix for popovers in main calendar on Apple touch devices ";
$changelog[$extension][$version]["bugfixes"][]      = "Editor sizing in layout editor";
$changelog[$extension][$version]["bugfixes"][]      = "Disable filter options when called from Joomla CLI";