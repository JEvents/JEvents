<?php
$version                                            = "3.6.65";
$date                                               = "2023-09-15";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["bugfixes"]        = array();

$changelog[$extension][$version]["features"][]      = "Add warning to event admin users when database collations need updating and mechanism to perform this update.  Needed to support emojis in event descriptions etc.";
$changelog[$extension][$version]["features"][]      = "Accessibility enhancement by ensuring modal popups have unique identifiers ";

$changelog[$extension][$version]["bugfixes"][]      = "Further minor fix to popup calendar formatting";
$changelog[$extension][$version]["bugfixes"][]      = "Fix for plugin parameters not picking up default values in JEvents config causing deprecation notice when error reporting was enabled";
$changelog[$extension][$version]["bugfixes"][]      = "Fix for transaction date and hence transaction saving in RSVP Pro ";
