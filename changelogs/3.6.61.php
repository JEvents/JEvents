<?php
$version                                            = "3.6.60";
$date                                               = "2023-06-29";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["bugfixes"]        = array();


$changelog[$extension][$version]["features"][]      = "New inline printing mechanism that doesn't use popup window - config driven";
$changelog[$extension][$version]["bugfixes"][]      = "Minor tooltip improvements for Bootstrap 5 in Joomla 4";
$changelog[$extension][$version]["bugfixes"][]      = "Change wording of filter button in list views from 'Search' to 'Filter option' to mirror Joomla 4 changes";
$changelog[$extension][$version]["bugfixes"][]      = "CSV Printing fix for some servers where the download size didn't match the size quoted";
$changelog[$extension][$version]["bugfixes"][]      = "A couple more strftime => date migration fixes";
