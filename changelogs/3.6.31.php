<?php
$version = "3.6.31";
$date = "2022-04-05";
$changelog["package_jevents"][$version] = array();
$changelog["package_jevents"][$version]["date"] = $date;
$changelog["package_jevents"][$version]["features"] = array();
$changelog["package_jevents"][$version]["features"][] = "Add optional framework selection in JEvents to allow usage of appropriate modals and popovers - especially important in uikit templates to set this";

$changelog["package_jevents"][$version]["bugfixes"] = array();
$changelog["package_jevents"][$version]["bugfixes"][]="Joomla 4 placeholder fix for event title";
$changelog["package_jevents"][$version]["bugfixes"][]="Include min JS files in package to make sure any stray ones are overwritten";
$changelog["package_jevents"][$version]["bugfixes"][]="Fix for conditional custom fields during step by step event creation";
$changelog["package_jevents"][$version]["bugfixes"][]="Switch parameter editing to use native Joomla showon";
$changelog["package_jevents"][$version]["bugfixes"][]="iCal import modal z-index fix for uikit sites";
$changelog["package_jevents"][$version]["bugfixes"][]="Fix for latest events module custom layout selector in Joomla 4";

