<?php
$version                                            = "3.6.96";
$date                                               = "2026-01-09";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["features"][]      = "Allow all category names to be translatable if stored in  capital letters";

$changelog[$extension][$version]["bugfixes"]        = array();
$changelog[$extension][$version]["bugfixes"][]      = "Fix for Joomla 6 installations stripping HTML from custom layouts when saved";