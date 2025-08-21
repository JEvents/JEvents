<?php
$version                                            = "3.6.91";
$date                                               = "2025-08-21";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["features"][]      = "Use webcals for secure connection to webcal";
$changelog[$extension][$version]["features"][]      = "New config option to allow you to NOT output category specific and date specific navigation links in the main component views - to reduce bot crawling!";
$changelog[$extension][$version]["features"][]      = "Add support for CATDESCS output code to show all matching category descriptions";

$changelog[$extension][$version]["bugfixes"]        = array();
$changelog[$extension][$version]["bugfixes"][]      = "JEV Layouts fix for Joomla 5 where jquery isn't loaded";
$changelog[$extension][$version]["bugfixes"][]      = "fix for codemirror being used as editor in layout page for latest events module";
$changelog[$extension][$version]["bugfixes"][]      = "Fix column preview images in custom layouts in Joomla 5 to not use jQuery";
$changelog[$extension][$version]["bugfixes"][]      = "Improve support for redirect to custom field URL in new window in more contexts";
