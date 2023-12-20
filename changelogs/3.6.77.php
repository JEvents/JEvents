<?php
$version                                            = "3.6.77";
$date                                               = "2023-12-20";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["features"][]      = "change rp_id field to bigint to allow for massive number of repeats or iCal updates";
$changelog[$extension][$version]["features"][]      = "Translations of layouts now styled as UIkit buttons";

$changelog[$extension][$version]["bugfixes"]        = array();
$changelog[$extension][$version]["bugfixes"][]      = "Improve PHP 8.2 support suppressing dynamic class values warning";
$changelog[$extension][$version]["bugfixes"][]      = "Make uid index 190 chars long";
$changelog[$extension][$version]["bugfixes"][]      = "Change query for lists of events showing only one repeat to handle irregular repeats and exceptions better";
$changelog[$extension][$version]["bugfixes"][]      = "Fix URL export in iCals (had a stray space at the start which Google calendar no longer deals with)";
$changelog[$extension][$version]["bugfixes"][]      = "Fix for csv import where RRULE column is missing";
