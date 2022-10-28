<?php
$version                                            = "3.6.43";
$date                                               = "2022-10-28";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["bugfixes"]        = array();

$changelog[$extension][$version]["features"][]      = "Publish state in layout and layout translation editing now looks like a proper dropdown";
$changelog[$extension][$version]["features"][]      = "Add category colour styling to Joomla 4 category views in backend";
$changelog[$extension][$version]["features"][]      = "Popover font sizes increased in the backend";
$changelog[$extension][$version]["features"][]      = "Sites that have valid club codes can hide unused component menu items";
$changelog[$extension][$version]["features"][]      = "Direct links to jevents modules from backend menu system";
$changelog[$extension][$version]["features"][]      = "Use x-alt-descriptions to pass HTML versions in iCal exports";
$changelog[$extension][$version]["features"][]      = "Use font-awesome icons in latest events module layouts for Joomla 4";
$changelog[$extension][$version]["features"][]      = "Load Joomla font-awesome css in Joomla 4 in bootstrap mode";
$changelog[$extension][$version]["features"][]      = "Fully disable smartphone theme when installed";

$changelog[$extension][$version]["bugfixes"][]      = "Fix for language and category specific layout usage";
$changelog[$extension][$version]["bugfixes"][]      = "Fix for closing left menu bar in backend when in click mode";
$changelog[$extension][$version]["bugfixes"][]      = "Trap for unset core.create access groups";
