<?php
$version = "3.6.26";
$date = "2022-02-07";
$changelog["package_jevents"][$version] = array();
$changelog["package_jevents"][$version]["date"] = $date;
$changelog["package_jevents"][$version]["features"] = array("Add codemirror overlay to highlight fields when editing layouts");

$changelog["package_jevents"][$version]["bugfixes"] = array();
$changelog["package_jevents"][$version]["bugfixes"][]="Fix layout editing - badly constructed minimised JS files was blocking field selection";