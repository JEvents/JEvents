<?php
$version                                            = "3.6.69";
$date                                               = "2023-11-07";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["bugfixes"]        = array();

$changelog[$extension][$version]["features"][]      = "Make sure new float options and timeline layout are supported";
$changelog[$extension][$version]["features"][]      = "Add support for preview images to illustrate layout options";
$changelog[$extension][$version]["features"][]      = "Add support for module specific theme option used in float theme";
$changelog[$extension][$version]["bugfixes"][]      = "Add root URI to failed calendar update notification emails";
$changelog[$extension][$version]["bugfixes"][]      = "Add new missing events email config option so that if link to an event doesn't exist and can't be found a specific message can be given to the visitor";
$changelog[$extension][$version]["bugfixes"][]      = "Add category information to the list of events view in the backend with links to a category specific filtered view";
$changelog[$extension][$version]["bugfixes"][]      = "Pass the matching module into the latest events module layout so that information from the module itself can be used in the layout - particularly important for float theme";

$changelog[$extension][$version]["bugfixes"][]      = "Fix for date iCal exports for exceptions where the first repeat has been changed following PHP 8+ strftime changes";
$changelog[$extension][$version]["bugfixes"][]      = "Make sure uid index has been removed if it was created previously - possibly throwing index length error message";






