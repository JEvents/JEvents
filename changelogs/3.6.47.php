<?php
$version                                            = "3.6.47";
$date                                               = "2022-13-13";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["bugfixes"]        = array();


$changelog[$extension][$version]["bugfixes"][]      = "Fix saving weekly repeat events starting in January 2023 - was creating multiple repeats on the same day.  The problem was caused by the week number for the first week in January being 52 according to ISO 8601";
$changelog[$extension][$version]["bugfixes"][]      = "DB execute instead of loadObjectList in onCategoryChangeState in content plugin.  Was causing issue with unpublishing categories";
$changelog[$extension][$version]["bugfixes"][]      = "Fix for running maintenance on JEvents Finder plugin";
