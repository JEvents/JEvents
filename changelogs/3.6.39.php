<?php
$version                                            = "3.6.39";
$date                                               = "2022-06-24";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["bugfixes"]        = array();


$changelog[$extension][$version]["bugfixes"][]      = "Extend missing event finder handling to post save event redirection - if you changed the category of an event in frontend editing it may not be visible on the current menu item without this.";
$changelog[$extension][$version]["bugfixes"][]      = "New categories should not have category admin set by default";
$changelog[$extension][$version]["bugfixes"][]      = "Latest events module to take account of multi-day events starting before 'today' when searching the database when using display on first day only mode";
$changelog[$extension][$version]["bugfixes"][]      = "Catch repeat creation error where RRULE field had extra stray comma at the end e.g. +1FR,";
$changelog[$extension][$version]["bugfixes"][]      = "Make calendar label 200 chars by default instead of just 30";
$changelog[$extension][$version]["bugfixes"][]      = "Fix for modal event search when creating single event menu item";
$changelog[$extension][$version]["bugfixes"][]      = "Reduce use of javascript 'let' in frontend which caused problems for some old iPads";
