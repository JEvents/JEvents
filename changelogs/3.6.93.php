<?php
$version                                            = "3.6.93";
$date                                               = "2025-10-28";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();

$changelog[$extension][$version]["bugfixes"]        = array();
$changelog[$extension][$version]["bugfixes"][]      = "Fix for Joomla 5.4 for custom layout editing";
$changelog[$extension][$version]["bugfixes"][]      = "Fix for Joomla 5.4 for saving configuration";
$changelog[$extension][$version]["bugfixes"][]      = "Workaround for icalImport where x-alt-desc includes raw text but is supposed to be HTML e.g. from civicrm";
