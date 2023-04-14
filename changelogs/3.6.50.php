<?php
$version                                            = "3.6.50";
$date                                               = "2023-04-14";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["bugfixes"]        = array();

$changelog[$extension][$version]["features"][]      = "New Save as Copy option when editing events";
$changelog[$extension][$version]["features"][]      = "Add raw event link to output options in custom layouts";
$changelog[$extension][$version]["features"][]      = "Showon to respond to input events and not just change in text fields - will make event editing slicker";
$changelog[$extension][$version]["features"][]      = "Add support for importing custom fields via iCal files as well as CSV";


$changelog[$extension][$version]["bugfixes"][]      = "Fix background colour output in list views";
$changelog[$extension][$version]["bugfixes"][]      = "Make sure byday isn't set with week numbers for weekly repeats";
$changelog[$extension][$version]["bugfixes"][]      = "Remove full bootstrap framework loading - leave this to Joomla bootstrap class/functions";
$changelog[$extension][$version]["bugfixes"][]      = "Convert %Z to active timezone when using STARTTZ and ENDTZ field outputs";
