<?php
$version                                            = "3.6.54";
$date                                               = "2023-05-05";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["bugfixes"]        = array();

$changelog[$extension][$version]["features"][]      = "Add support for {MODIFIED} date in event detail custom layouts";
$changelog[$extension][$version]["features"][]      = "New config option in Filter Module to auto-submit on all field changes";
$changelog[$extension][$version]["features"][]      = "Add support for date formatting in CREATED and MODIFIED output fields";

$changelog[$extension][$version]["bugfixes"][]      = "Fix for weekly event repetitions throwing bogus warning about overlapping repeats";
$changelog[$extension][$version]["bugfixes"][]      = "Fix for frontend iCal import in Joomla 4";
$changelog[$extension][$version]["bugfixes"][]      = "Stop showon being very slow for event title changes";
$changelog[$extension][$version]["bugfixes"][]      = "Remove use of PHP strftime as a function";
$changelog[$extension][$version]["bugfixes"][]      = "Fix for showon script to work in managed locations config";




