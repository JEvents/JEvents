<?php
$version                                            = "3.6.38";
$date                                               = "2022-06-07";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["bugfixes"]        = array();


$changelog[$extension][$version]["features"][]      = "New config option to ALWAYS show left menu icons";
$changelog[$extension][$version]["features"][]      = "New JEvents filter option to use UIKit styling for form elements";
$changelog[$extension][$version]["features"][]      = "Add custom css option for filter module";


$changelog[$extension][$version]["bugfixes"][]      = "Correct showon for tabs in event editing in Joomla 4";
$changelog[$extension][$version]["bugfixes"][]      = "Fix showon for plugin parameters in JEvents config";
$changelog[$extension][$version]["bugfixes"][]      = "Support background - foreground contrast colours using 3 character hex colours";
$changelog[$extension][$version]["bugfixes"][]      = "Custom Event editing page was not picking up custom css or js";
$changelog[$extension][$version]["bugfixes"][]      = "Fix missing ob_start() in system messages layout";
$changelog[$extension][$version]["bugfixes"][]      = "Migrate module calendar navigation to native javascript";
$changelog[$extension][$version]["bugfixes"][]      = "";
