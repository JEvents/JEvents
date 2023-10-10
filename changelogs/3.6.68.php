<?php
$version                                            = "3.6.68";
$date                                               = "2023-10-10";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["bugfixes"]        = array();

$changelog[$extension][$version]["features"][]      = "Add ability to clean up orphaned latest events module custom layouts";
$changelog[$extension][$version]["features"][]      = "Let Float theme be used on more menu item types - day list, week list and month calendar";
$changelog[$extension][$version]["features"][]      = "Add notification to calendar owner if auto-update didn't succeed";

$changelog[$extension][$version]["bugfixes"][]      = "Include published state when fetching first repeat of events - the field is needed by some addons";
$changelog[$extension][$version]["bugfixes"][]      = "Skip collations update for RSVP Pro invitee list and jev_notifications tables";
$changelog[$extension][$version]["bugfixes"][]      = "Fix Selecting categories for ical export does only export the first category when using custom category separator - thanks to csporer";
$changelog[$extension][$version]["bugfixes"][]      = "Fix for Finder plugin throwing invalid date format when configuration has not been saved";
