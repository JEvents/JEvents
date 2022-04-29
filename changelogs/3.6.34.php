<?php
$version                                            = "3.6.34";
$date                                               = "2022-04-29";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["features"][]      = "Add save to MS Outlook/Outlook live links";
$changelog[$extension][$version]["features"][]      = "Allow Finder plugin to play well with hidden event detail plugin";
$changelog[$extension][$version]["features"][]      = "Switch loading of JEvents news to use Javascript XHR request";

$changelog[$extension][$version]["bugfixes"]        = array();
$changelog[$extension][$version]["bugfixes"][]      = "Fix to conditional display of blocks when using field by field editing with zero tab event editing";
$changelog[$extension][$version]["bugfixes"][]      = "Fix for JCE editor not triggering test on empty required description field";
$changelog[$extension][$version]["bugfixes"][]      = "Trap for occasional bad PHP quoted_printable_decode problems on event importing";







