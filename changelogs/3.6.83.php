<?php
$version                                            = "3.6.83";
$date                                               = "2024-09-06";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["features"][]      = "New multi-category filter for filter module";
$changelog[$extension][$version]["features"][]      = "Eliminate the need for Joomla 5 B/C plugin - but don't disable until the plugins that you use have all been update";
$changelog[$extension][$version]["features"][]      = "add TITLE_ADDSLASHES output option to layout editor";
$changelog[$extension][$version]["features"][]      = "fix for next/prev event links on menu items with category restrictions";
$changelog[$extension][$version]["features"][]      = "reinstate rss icon which has been dropped by Joomla";


$changelog[$extension][$version]["bugfixes"]        = array();
$changelog[$extension][$version]["bugfixes"][]      = "Eliminate a slew of PHP 8.3 deprecation errors";
$changelog[$extension][$version]["bugfixes"][]      = "Make sure redirection to event detail after save is a SEF enabled URL";
