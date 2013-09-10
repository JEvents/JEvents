<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class modJeventsCreateHelper {

    public function __construct() {
        $file = JPATH_SITE . '/components/com_jevents/mod.defines.php';
        if (file_exists($file)) {
            include_once($file);
            include_once(JPATH_SITE . "/components/com_jevents/libraries/modfunctions.php");
        } else {
            die(JText::_('JEV_LATEST_NEEDS_COMPONENT'));
        }
        JEVHelper::loadLanguage('modlatest');
    }

}
