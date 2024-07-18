<?php
$version                                            = "3.6.82";
$date                                               = "2024-07-18";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["features"][]      = "New config option for default behaviour of published filter in frontend admin panel";
$changelog[$extension][$version]["features"][]      = "Add category ID {{CATID}} as output field in layout editor";
$changelog[$extension][$version]["features"][]      = "Add support for legend plugin in year list views";

$changelog[$extension][$version]["bugfixes"]        = array();
$changelog[$extension][$version]["bugfixes"][]      = "Fix for backend toolbar styling in Joomla 5";
$changelog[$extension][$version]["bugfixes"][]      = "Fix for frontend iCal Import on some sites where the SEF settings that caused the FROM target to redirect";
$changelog[$extension][$version]["bugfixes"][]      = "Better styling if iCal import on UIkit sites";
$changelog[$extension][$version]["bugfixes"][]      = "Fix for styling of filter module in bootstrap 5 sites";