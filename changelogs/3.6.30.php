<?php
$version = "3.6.30";
$date = "2022-02-25";
$changelog["package_jevents"][$version] = array();
$changelog["package_jevents"][$version]["date"] = $date;
$changelog["package_jevents"][$version]["features"] = array();
$changelog["package_jevents"][$version]["features"][] = "Add delete confirm message in manage events list views";

$changelog["package_jevents"][$version]["bugfixes"] = array();
$changelog["package_jevents"][$version]["bugfixes"][]="Extra check for isRepeat function to cover situation where freq = none";
$changelog["package_jevents"][$version]["bugfixes"][]="Set data index for zero values correctly in gsl-select was seeing zero as empty and using -1 instead";
$changelog["package_jevents"][$version]["bugfixes"][]="if using newfrontend interface or in tha beackend make sure we use gslselect for multi-select";
$changelog["package_jevents"][$version]["bugfixes"][]="Hide external link icon in backend (Joomla 4.1 layout issue)";
$changelog["package_jevents"][$version]["bugfixes"][]="Fix date select event for irregular repeats";