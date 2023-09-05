<?php
$version                                            = "3.6.62";
$date                                               = "2023-09-05";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["bugfixes"]        = array();

$changelog[$extension][$version]["bugfixes"][]      = "Fix getting start date in event list view throwing a PHP deprecation notice";
