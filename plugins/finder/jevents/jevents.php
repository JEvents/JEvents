<?php
/**
 * @copyright   copyright (C) 2012-JEVENTS_COPYRIGHT GWESystems Ltd - All rights reserved
 *
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_BASE') or die;

if (version_compare(JVERSION, '3.99.99', ">"))
{
	include_once 'jevents4.php';
}
else
{
	include_once 'jevents3.php';
}