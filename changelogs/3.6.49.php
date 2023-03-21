<?php
$version                                            = "3.6.49";
$date                                               = "2023-02-21";
$extension                                          = "package_jevents";
$changelog[$extension][$version]                    = array();
$changelog[$extension][$version]["date"]            = $date;
$changelog[$extension][$version]["features"]        = array();
$changelog[$extension][$version]["bugfixes"]        = array();

$changelog[$extension][$version]["features"][]      = "Add support for event counts by category in category view with links";
$changelog[$extension][$version]["features"][]      = "Mechanism to inject theme specific config options into module and menu item config";


$changelog[$extension][$version]["bugfixes"][]      = "Fix print icon in list views for uikit theme";
$changelog[$extension][$version]["bugfixes"][]      = "Use Joomla 4 pagination links in event and categories views in backend";
$changelog[$extension][$version]["bugfixes"][]      = "Better error message in if config option of custom field is broken in Joomla 4 e.g. caused by out of date plugin or missing field type definition";
$changelog[$extension][$version]["bugfixes"][]      = "CSV export in frontend no longer uses redirect to fix too small an item limit - helps on some servers which were not dealing with the redirect well";
$changelog[$extension][$version]["bugfixes"][]      = "STARTTZ and ENDTZ do not show date or time for all day or no-end day events";
$changelog[$extension][$version]["bugfixes"][]      = "Handle new class of menu item specific parameters used in some club themes";


