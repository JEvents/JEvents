<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: mod.defines.php 3059 2011-12-01 12:25:04Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined('_JEXEC') or die('No Direct Access');

use Joomla\CMS\Date\Date;


if (!defined("JEV_COM_COMPONENT"))
{
	define("JEV_COM_COMPONENT", "com_jevents");
	define("JEV_COMPONENT", str_replace("com_", "", JEV_COM_COMPONENT));
}

if (!defined("JEV_LIBS")){
	define("JEV_ADMINPATH", JPATH_ADMINISTRATOR . "/components/" . JEV_COM_COMPONENT . "/");
	define("JEV_PATH", JPATH_SITE . "/components/" . JEV_COM_COMPONENT . "/");
	define("JEV_VIEWS", JEV_PATH . "views");
	define("JEV_LIBS", JEV_PATH . "libraries");
	define("JEV_HELPERS", JEV_PATH . "helpers/");
	define("JEV_ABSTRACTEMPLATES", JEV_VIEWS . "/abstract/tmpl/");
	define("JEV_ADMINLIBS", JEV_ADMINPATH . "libraries/");
}

// PSR-4 namespace roots — shared classes under JEvents\, site-only under JEvents\Site\,
// admin-only under JEvents\Administrator\. Both roots registered so all three sub-namespaces
// resolve correctly whether running from site or admin context.
JLoader::registerNamespace('JEvents', JEV_PATH . 'libraries/src', false, false);
JLoader::registerNamespace('JEvents', JEV_ADMINPATH . 'libraries/src', false, false);

JLoader::registerAlias('JevRegistry', 'JEvents\\Site\\Registry', '7.0');


JLoader::registerAlias('JEVConfig', 'JEvents\\Config', '7.0');

JLoader::registerAlias('JEVHelper', 'JEvents\\Helper', '7.0');
JLoader::registerAlias('JevModuleHelper', 'JEvents\\ModuleHelper', '7.0');

JLoader::registerAlias('JevDate', 'JEvents\\Date', '7.0');
JLoader::registerAlias('JEventDate', 'JEvents\\Date', '7.0');
JLoader::registerAlias('JEventsHTML', 'JEvents\\HtmlHelper', '7.0');

JLoader::registerAlias('JEventsVersion', 'JEvents\\Version', '7.0');
JLoader::registerAlias('JevJoomlaVersion', 'JEvents\\JevJoomlaVersion', '7.0');

JLoader::registerAlias('catLegend', 'JEvents\\Site\\CatLegend', '7.0');

JLoader::registerAlias('JEventsDBModel', 'JEvents\\DBModel', '7.0');
JLoader::registerAlias('JEventsDataModel', 'JEvents\\DataModel', '7.0');

JLoader::registerAlias('jEventCal', 'JEvents\\EventCal', '7.0');
JLoader::registerAlias('jIcal', 'JEvents\\ICal', '7.0');
JLoader::registerAlias('jIcalEventDB', 'JEvents\\IcalEventDB', '7.0');
JLoader::registerAlias('jIcalEventRepeat', 'JEvents\\IcalEventRepeat', '7.0');

JLoader::registerAlias('JEventsAbstractView', 'JEvents\\Administrator\\View\\AbstractView', '7.0');

JLoader::registerAlias('jevFilterProcessing', 'JEvents\\FilterProcessing', '7.0');

// TODO retire this sometime?
JLoader::register('JEV_CommonFunctions', JEV_PATH . "/libraries/commonfunctions.php");
