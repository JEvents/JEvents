<?php
$version                                            = "3.6.40";
$date                                               = "2022-08-03";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["bugfixes"]        = array();

$changelog[$extension][$version]["features"][]      = "Finder plugin picks up images";
$changelog[$extension][$version]["features"][]      = "Improve style and functionality of iCal export menu item";

$changelog[$extension][$version]["bugfixes"][]      = "nodeList.forEach polyfill";
$changelog[$extension][$version]["bugfixes"][]      = "JustMine filter should be ignore when called in module which is configured to ignore filter module";
$changelog[$extension][$version]["bugfixes"][]      = "Catch blank catidsIn value in setupComponentCatids";
$changelog[$extension][$version]["bugfixes"][]      = "Improve handling of multiday events in day list views to mirror latest events module capabilities";
$changelog[$extension][$version]["bugfixes"][]      = "Fix for editing an event and changing a custom field value which then blocks the display of the event being edited";
$changelog[$extension][$version]["bugfixes"][]      = "Fix for finder in Joomla 4";
$changelog[$extension][$version]["bugfixes"][]      = "Remove usage of quoted-print1able encoding in iCal exports";
$changelog[$extension][$version]["bugfixes"][]      = "More popover fixes for event editing pages";
$changelog[$extension][$version]["bugfixes"][]      = "Monthly Week number selections now reflect date picked when creating events";
$changelog[$extension][$version]["bugfixes"][]      = "Fix weeknum initial setting in monthly repeat when changing frequency type";
$changelog[$extension][$version]["bugfixes"][]      = "Fix mini-calendar navigation for some iPhone";
$changelog[$extension][$version]["bugfixes"][]      = "Make sure modals work for old 'rel' based attributes e.g. in RSVP Pro ticket popup";
$changelog[$extension][$version]["bugfixes"][]      = "Fix for popovers not showing when editing JEvents menu items";