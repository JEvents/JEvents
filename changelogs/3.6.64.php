<?php
$version                                            = "3.6.64";
$date                                               = "2023-09-12";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["bugfixes"]        = array();

$changelog[$extension][$version]["bugfixes"][]      = "Avoid php notices for passing null values to core methods like trim()";
$changelog[$extension][$version]["bugfixes"][]      = "Fix for missing onchange handler for calendar fields (caused by recent changes in Joomla popup calendar)";
$changelog[$extension][$version]["bugfixes"][]      = "New direct strftime to date output function";
