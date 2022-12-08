<?php
$version                                            = "3.6.44";
$date                                               = "2022-11-07";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["bugfixes"]        = array();

$changelog[$extension][$version]["features"][]      = "Make Bootstrap 5 popover pick up HTML content of tooltip and also event colours for headings";
$changelog[$extension][$version]["features"][]      = "Option to add prefix to iCal exported event titles - can be used to signify the source calendar";
$changelog[$extension][$version]["features"][]      = "Implement  category specific event edit layouts making sure they pick up custom JS and custom CSS";

$changelog[$extension][$version]["bugfixes"][]      = "Fix category list syling for Joomla 4";
$changelog[$extension][$version]["bugfixes"][]      = "Joomla 4 stop using Itemid=1 for default mnu item in modules";
$changelog[$extension][$version]["bugfixes"][]      = "Fix Jumpto form link for non-club themes + Ruthin";
$changelog[$extension][$version]["bugfixes"][]      = "Fix for advanced module manager and new styling and JS functionality in administrator e.g. when selecting matching events or categories";
$changelog[$extension][$version]["bugfixes"][]      = "Move tab handling out of event edit page and into the layout processor - where were some problems with tabbed editing pages in some frameworks before this in Joomla 4";
$changelog[$extension][$version]["bugfixes"][]      = "Fix for users editing their own events when they can't publish them";
$changelog[$extension][$version]["bugfixes"][]      = "Increase default length of location and contact fields to support imports from calendars where these fields are long";





