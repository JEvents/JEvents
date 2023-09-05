<?php
$version                                            = "3.6.61";
$date                                               = "2023-06-29";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["bugfixes"]        = array();


$changelog[$extension][$version]["features"][]      = "fix for 00:00:00 added by electric calendar filter";
$changelog[$extension][$version]["features"][]      = "fix for date selector not responding to auto-submis config option in filter module";
$changelog[$extension][$version]["features"][]      = "add dbfix method to create missing columns if needed";
$changelog[$extension][$version]["features"][]      = "Do not display regex version of search when using a regex search";
$changelog[$extension][$version]["features"][]      = "do not call unixTime as a static method";
$changelog[$extension][$version]["features"][]      = "Fix for categories not being created if ignore embedded categories is not set (conditional config setttings/showon could cause this to happen)";
$changelog[$extension][$version]["features"][]      = "set max height and auto-scroll on category selector - workaround for problem from updated uikit";
$changelog[$extension][$version]["features"][]      = "Fix for date/time selector in calendar popup - used in custom fields and RSvP Pro";
$changelog[$extension][$version]["features"][]      = "Fix for importing custom field values in iCal files";
$changelog[$extension][$version]["features"][]      = "Fix for German date styling outputting %s when using PHP 8.3";
$changelog[$extension][$version]["features"][]      = "Make sure export form respects time limit plugin impact on menu item";
$changelog[$extension][$version]["features"][]      = "Customisable layout for JEvents Filter module - new config option in Filter module";