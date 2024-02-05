<?php
$version                                            = "3.6.78";
$date                                               = "2023-02-05";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["features"][]      = "Better styling of category management page in Joomla 3";
$changelog[$extension][$version]["features"][]      = "Add support for x-alt-desc values in iCal import";
$changelog[$extension][$version]["features"][]      = "Add RPID and EVID output columns options for list view";

$changelog[$extension][$version]["bugfixes"]        = array();
$changelog[$extension][$version]["bugfixes"][]      = "Trap bad IntlDateFormat value with informational warning message";
$changelog[$extension][$version]["bugfixes"][]      = "Remove use of JResponse class in RSS feed data source";
$changelog[$extension][$version]["bugfixes"][]      = "Fix colour picker in Joomla 5 dark mode";
$changelog[$extension][$version]["bugfixes"][]      = "Convert URLs in event description to absolute URLS in iCal export";
$changelog[$extension][$version]["bugfixes"][]      = "Avoid missing Trunactor class when ht_strlen is available ";
$changelog[$extension][$version]["bugfixes"][]      = "Workaround browsers not allowing iframe about:blank URLs for empty pages";
