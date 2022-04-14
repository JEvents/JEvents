<?php
$version = "3.6.33";
$date = "2022-04-14";
$changelog["package_jevents"][$version] = array();
$changelog["package_jevents"][$version]["date"] = $date;
$changelog["package_jevents"][$version]["features"] = array();
$changelog["package_jevents"][$version]["bugfixes"] = array();

$changelog["package_jevents"][$version]["features"][] = "Stop using 'eval' in calendar navigation in mini-calendar JS";

$changelog["package_jevents"][$version]["bugfixes"][]="Fix for modal javascript errors in release 3.6.31";
$changelog["package_jevents"][$version]["bugfixes"][]="JS error in enddate change when start date is tested";
$changelog["package_jevents"][$version]["bugfixes"][]="Add try/catch around sending admin emails to stop problems on sites with emailing disabled";
$changelog["package_jevents"][$version]["bugfixes"][]="Fix for weekly repeat rule when sunday is selected and monday is first day of the week";
$changelog["package_jevents"][$version]["bugfixes"][]="Typeahead library styling fix for UIkit templates";
$changelog["package_jevents"][$version]["bugfixes"][]="Force Bootstrap to be loaded in Joomla 4 when bootstrap is the specified style in case the template isn't loading it";
$changelog["package_jevents"][$version]["bugfixes"][]="";
