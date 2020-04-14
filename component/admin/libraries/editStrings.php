<?php

use Joomla\CMS\Language\Text;

/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: editStrings.php 3576 2012-05-01 14:11:04Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
$editStrings = " // JEvents Language Srings\n";
$editStrings .= "var handm = '" . Text::_("Hours_and_Minutes", true) . "';\n";
$editStrings .= "var invalidtime = '" . Text::_("Invalid_Time", true) . "';\n";
$editStrings .= "var invalidcorrected = '" . Text::_("INVALID_CORRECTED", true) . "';\n";
$editStrings .= "var jevyears= '" . Text::_("years", true) . "';\n";
$editStrings .= "var jevmonths= '" . Text::_("months", true) . "';\n";
$editStrings .= "var jevweeks= '" . Text::_("weeks", true) . "';\n";
$editStrings .= "var jevdays= '" . Text::_("days", true) . "';\n";
$editStrings .= "var jevirregular= '" . Text::_("irregular", true) . "';\n";

$editStrings .= " // end JEvents Language Srings\n";
