<?php
$version                                            = "3.6.65";
$date                                               = "2023-09-12";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["bugfixes"]        = array();

$changelog[$extension][$version]["bugfixes"][]      = "Fix event date editing for sites using D/M/Y date formats for dates beyond 12th of the month.";
