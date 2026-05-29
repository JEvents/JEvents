<?php
$version                                            = "3.6.102";
$date                                               = "2026-06-29";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();

$changelog[$extension][$version]["bugfixes"]        = array();
$changelog[$extension][$version]["bugfixes"][]      = "Fix for daily repeat generation that runs beyond the end of 2026";
