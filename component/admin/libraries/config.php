<?php

/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: config.php 2490 2011-08-24 14:15:21Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2017 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * convenience wrapper for config - to ensure backwards compatability
 */
// on some servers with Xcache both classes seem to be 'compiled' and it throws an error but if we add this second test its ok - go figure .
if (!defined("JEVCONFIG")) {
    define("JEVCONFIG", 1);

    class JEVConfig {

        // 1.6 mod
        static function &getInstance($inifile = '') {
            $params = JComponentHelper::getParams("com_jevents");
            return $params;
        }
    }
}
