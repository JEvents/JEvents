<?php
$version                                            = "3.6.48";
$date                                               = "2023-02-10";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["bugfixes"]        = array();

$changelog[$extension][$version]["features"][]      = "Option to check for self-overlapping repeats when saving event";
$changelog[$extension][$version]["features"][]      = "Pass category description through content plugins";


$changelog[$extension][$version]["bugfixes"][]      = "Force bootstrap modals to appear in frontend for template with bad javascript/css implentation";
$changelog[$extension][$version]["bugfixes"][]      = "Move publish event above edit in frontend dialog";
$changelog[$extension][$version]["bugfixes"][]      = "Fix for LD Json output in certain customised layouts";
$changelog[$extension][$version]["bugfixes"][]      = "Fix jump to in Flat theme";
