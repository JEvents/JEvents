<?php
$version                                            = "3.6.84";
$date                                               = "2025-01-15";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["features"][]      = "Strict category language config option - When editing an event only offer categories visible in all languages or the current language in use when editing an event";
$changelog[$extension][$version]["features"][]      = "Allow enforcement of strict URL matching for id and title together in SEF matching to avoid URL guessing";
$changelog[$extension][$version]["features"][]      = "Improvements to SEF Urls for smart search results";

$changelog[$extension][$version]["bugfixes"]        = array();
$changelog[$extension][$version]["bugfixes"][]      = "PHP 8.x deprecation messages";
$changelog[$extension][$version]["bugfixes"][]      = "Make sure dtstart and dtend are bigint to allow for far dated events";
