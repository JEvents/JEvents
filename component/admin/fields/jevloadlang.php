<?php

/**
 * JEvents Locations Component for Joomla 1.5.x
 *
 * @version     $Id: jevboolean.php 1331 2010-10-19 12:35:49Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is included in Joomla!

defined('JPATH_BASE') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
FormHelper::loadFieldClass('spacer');

/**
 * JevModule Load Language class for the JEvents Component
 *
 * @package      JEvents.fields
 * @subpackage   modules
 * @since        1.6
 */
class JFormFieldJevloadlang extends JFormFieldSpacer
{
	protected $type = 'jevloadlang';

	public function getinput()
	{

		include_once(JPATH_ADMINISTRATOR . "/components/com_jevents/jevents.defines.php");
		$lang = Factory::getLanguage();

		$lang->load("com_jevents", JPATH_ADMINISTRATOR);
		$lang->load("mod_jevents_latest", JPATH_SITE);
		$lang->load("mod_jevents_latest", JPATH_SITE, "en-GB");

		if (Text::_("JEV_LATEST_OVERRIDE_LAYOUT") == "JEV_LATEST_OVERRIDE_LAYOUT")
		{
			$lang->load("mod_jevents_latest", JPATH_SITE, "en-GB");
		}

	}

}
