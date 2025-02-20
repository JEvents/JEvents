<?php
$version                                            = "3.6.85";
$date                                               = "2025-02-20";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["features"][]      = "Improved caching settings in modules to work better with page caching";

$changelog[$extension][$version]["bugfixes"]        = array();
$changelog[$extension][$version]["bugfixes"][]      = "Avoid plugins being called multiple time when called from YOOTheme";
$changelog[$extension][$version]["bugfixes"][]      = "Fix for undefined limitstart in latest events module ";
$changelog[$extension][$version]["bugfixes"][]      = "Include missing min.js files in package";
