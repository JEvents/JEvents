<?php
$version = "3.6.26";
$date = "2022-02-14";
$changelog["package_jevents"][$version] = array();
$changelog["package_jevents"][$version]["date"] = $date;
$changelog["package_jevents"][$version]["features"] = array();
$changelog["package_jevents"][$version]["features"][] = "Add codemirror overlay to highlight fields when editing layouts";
$changelog["package_jevents"][$version]["features"][] = "Remove phoca admin menu in Joomla - positioning messes with JEvents";
$changelog["package_jevents"][$version]["features"][] = "Allow uikit tooltips to be used in Joomla 4";

$changelog["package_jevents"][$version]["bugfixes"] = array();
$changelog["package_jevents"][$version]["bugfixes"][]="Fix layout editing - badly constructed minimised JS files was blocking field selection";
$changelog["package_jevents"][$version]["bugfixes"][]="PHP 8.1 check for array before imploding causes fatal error on non-array.";
$changelog["package_jevents"][$version]["bugfixes"][]="Make latest events module target menu item strict, we shouldn't be able to select a non-jevents menu item for the target menu item.";
$changelog["package_jevents"][$version]["bugfixes"][]="Check radio field exists before including it";
$changelog["package_jevents"][$version]["bugfixes"][] = "Dashboard panel to load language strings";
