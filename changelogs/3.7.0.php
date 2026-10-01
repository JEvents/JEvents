<?php
$version                                            = "3.7.0";
$date                                               = "2026-10-01";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["features"][]      = "Namespacing JEvents classes";
$changelog[$extension][$version]["features"][]      = "config option to allow support for raw html fields in addon descriptions e.g. managed people and locations";
$changelog[$extension][$version]["features"][]      = "Event list view now offers better options to allow it to be used for frontend managment of events e.g. event status, link to manage event, list of event repeats and a list of events create by the same creator";
$changelog[$extension][$version]["features"][]      = "Permission setting to use WYSIWYG editor";
$changelog[$extension][$version]["features"][]      = "When editing frontend events in a popop there is now a config option to allow reloading the source page automatically on save/cancel";

$changelog[$extension][$version]["bugfixes"]        = array();
$changelog[$extension][$version]["bugfixes"][]      = "change how we store jevdata in form";
$changelog[$extension][$version]["bugfixes"][]      = "minor security enhanced for iCalImport";
