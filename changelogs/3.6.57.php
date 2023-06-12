<?php
$version                                            = "3.6.57";
$date                                               = "2023-06-12";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["bugfixes"]        = array();

$changelog[$extension][$version]["features"][]      = "Make iCal export URLs nofollow";
$changelog[$extension][$version]["bugfixes"][]      = "Move more of strftime usage to date function";
