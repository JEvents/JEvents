<?php
$changelog["package_jevents"]["3.6.14"] = array();
$changelog["package_jevents"]["3.6.14"]["date"] = "2021-05-26";
$changelog["package_jevents"]["3.6.14"]["features"] = array();
$changelog["package_jevents"]["3.6.14"]["features"][]="Use gslselect replacement instead of chosen where possible in event editing";
$changelog["package_jevents"]["3.6.14"]["features"][]="Added TZID as an custom layout field";
$changelog["package_jevents"]["3.6.14"]["features"][]="Add link to YourSites (when installed and enabled)";
$changelog["package_jevents"]["3.6.14"]["features"][]="Electric calendar can leave the date field blank now - needed for RSVP Pro coupon fixes";
$changelog["package_jevents"]["3.6.14"]["features"][]="Add option to merge 2 columns together in list views if they share the same heading";
$changelog["package_jevents"]["3.6.14"]["features"][]="Add support for Joomla custom field in the menu and to use our uikit wrapper";
$changelog["package_jevents"]["3.6.14"]["bugfixes"] = array();
$changelog["package_jevents"]["3.6.14"]["bugfixes"][]="Do not redirect to club addon components from component menu if they are not installed/enabled";
$changelog["package_jevents"]["3.6.14"]["bugfixes"][]="workaround for Joomla 3.9.26 breaking onchange handler for calendar fields";
$changelog["package_jevents"]["3.6.14"]["bugfixes"][]="Only load editicalGSL.js in frontend if not MSIE10 and new frontend editing is enabled";
$changelog["package_jevents"]["3.6.14"]["bugfixes"][]="Fix for double entry of irregular dates for date picker (workaround for bug in Joomla 3.9.26)";
$changelog["package_jevents"]["3.6.14"]["bugfixes"][]="Close previous modal when opening new modal from event edit modal";
$changelog["package_jevents"]["3.6.14"]["bugfixes"][]="Add custom repeat now adds it one day after the last repeat to avoid duplicate date/time check problems, it always uses the default detail id avoiding problems where the last repeat is an exception";
$changelog["package_jevents"]["3.6.14"]["bugfixes"][]="Work to support bootstrap 4 float theme and not to block bootstrap 4 modals";
$changelog["package_jevents"]["3.6.14"]["bugfixes"][]="Trap for situation where someone has deleted some default layouts";
$changelog["package_jevents"]["3.6.14"]["bugfixes"][]="Fix for boolean params not showing on some sites in the main config";
$changelog["package_jevents"]["3.6.14"]["bugfixes"][]="Fix for EXTRA INFO field not appearing in customised event editing layouts";
$changelog["package_jevents"]["3.6.14"]["bugfixes"][]="Set new default custom edit page to reflect new GSL Styling";



