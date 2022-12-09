<?php
$version                                            = "3.6.45";
$date                                               = "2022-11-09";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["bugfixes"]        = array();


$changelog[$extension][$version]["bugfixes"][]      = "Fix the way that we increased default length of location and contact fields to support imports from calendars where these fields are long - some servers reported that the index was too long";





