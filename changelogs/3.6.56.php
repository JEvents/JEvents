<?php
$version                                            = "3.6.56";
$date                                               = "2023-05-24";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["bugfixes"]        = array();

$changelog[$extension][$version]["bugfixes"][]      = "Fix to strftime replacement function which was breaking some iCal imports generating invalid dates";
