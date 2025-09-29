<?php
$version                                            = "3.6.92";
$date                                               = "2025-09-29";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["features"][]      = "Changes to header file to allow layout overrides for backend left and right bar";

$changelog[$extension][$version]["bugfixes"]        = array();
$changelog[$extension][$version]["bugfixes"][]      = "Revert changes to use webcals in secure urls";
$changelog[$extension][$version]["bugfixes"][]      = "Fix to getjson methods for Joomla 5 - relevant to specialist usage to extract event data in JSON format for export";
$changelog[$extension][$version]["bugfixes"][]      = "Remove duplicate list limit form element in backend events overview";
$changelog[$extension][$version]["bugfixes"][]      = "\e preg_replace modifier is no longer supported fir vCal imports";