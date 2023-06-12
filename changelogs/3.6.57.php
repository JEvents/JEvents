<?php
$version                                            = "3.6.57";
$date                                               = "2023-06-12";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["bugfixes"]        = array();

$changelog[$extension][$version]["features"][]      = "Config option to use custom css file in backend event editing ";
$changelog[$extension][$version]["features"][]      = "Make iCal export URLs nofollow";
$changelog[$extension][$version]["features"][]      = "UIkit version update - needed for YooTheme sites when editing events";
$changelog[$extension][$version]["features"][]      = "Add optional use of icalimportkey for reloading anonymously";
$changelog[$extension][$version]["features"][]      = "Support for %b, %B and %h format in calendar fields";

$changelog[$extension][$version]["bugfixes"][]      = "Move more of strftime usage to date function";
$changelog[$extension][$version]["bugfixes"][]      = "Fix backend category list view filtering by level and published state";
$changelog[$extension][$version]["bugfixes"][]      = "Fix php notice in outlook export urls";
$changelog[$extension][$version]["bugfixes"][]      = "Fix php notice in outlook export urls";
$changelog[$extension][$version]["bugfixes"][]      = "Fix positioning of close icon in popup modals for BS5";





