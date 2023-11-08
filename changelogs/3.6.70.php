<?php
$version                                            = "3.6.70";
$date                                               = "2023-11-08";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["bugfixes"]        = array();

$changelog[$extension][$version]["features"][]      = "iCal Export of irregular repeats is now supported - they are exported as a series of individual separate events";
$changelog[$extension][$version]["features"][]      = "Modal popups of images no longer use and IFrame and should resize to match the content where possible";

$changelog[$extension][$version]["bugfixes"][]      = "Fix installation issues in 3.6.69 release for new installs";







