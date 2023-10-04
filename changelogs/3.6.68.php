<?php
$version                                            = "3.6.68";
$date                                               = "2023-10-03";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["bugfixes"]        = array();

$changelog[$extension][$version]["features"][]      = "";

$changelog[$extension][$version]["bugfixes"][]      = "Include published state when fetching first repeat of events - the field is needed by some addons";
$changelog[$extension][$version]["bugfixes"][]      = "Skip collations update for RSVP Pro invitee list table";
$changelog[$extension][$version]["bugfixes"][]      = "Fix Selecting categories for ical export does only export the first category when using custom category separator - thanks to csporer";
$changelog[$extension][$version]["bugfixes"][]      = "";
