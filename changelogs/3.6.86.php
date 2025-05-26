<?php
$version                                            = "3.6.86";
$date                                               = "2025-03-25";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["features"][]      = "Add support for ALLCATEGORIES_CAT_BACKGROUND_COLOURED output";

$changelog[$extension][$version]["bugfixes"]        = array();
$changelog[$extension][$version]["bugfixes"][]      = "tmpl=component needs to check for acym as well as acymailing in generating detail link";
$changelog[$extension][$version]["bugfixes"][]      = "Fix checkconflict script for overlapping event check in Joomla 5 sites with multiple language categories";
$changelog[$extension][$version]["bugfixes"][]      = "Do not show language in category name for filters when the category had the same language as the live language";

