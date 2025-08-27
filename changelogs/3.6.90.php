<?php
$version                                            = "3.6.90";
$date                                               = "2025-06-06";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["features"][]      = "New option to support opening events directly in new window as specified in the custom fields addon.  Not just a redirect from the event detail page.";

$changelog[$extension][$version]["bugfixes"]        = array();
$changelog[$extension][$version]["bugfixes"][]      = "Fix for category filtering and category based URLs ";
$changelog[$extension][$version]["bugfixes"][]      = "Workaround for bigint field test on non INNODB database tables";

