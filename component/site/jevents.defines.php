<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: jevents.defines.php 3090 2011-12-11 10:00:17Z geraintedwards $
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

if (!defined("JEV_LIBS"))
{
	define("JEV_ADMINPATH", JPATH_ADMINISTRATOR . "/components/" . JEV_COM_COMPONENT . "/");
	define("JEV_PATH", JPATH_SITE . "/components/" . JEV_COM_COMPONENT . "/");
	define("JEV_VIEWS", JEV_PATH . "views");
	define("JEV_LIBS", JEV_PATH . "libraries");
	define("JEV_HELPERS", JEV_PATH . "helpers/");
	define("JEV_ABSTRACTEMPLATES", JEV_VIEWS . "/abstract/tmpl/");
	define("JEV_ADMINLIBS", JEV_ADMINPATH . "libraries/");
}
JLoader::register('JSite', JPATH_SITE . '/includes/application.php');

JLoader::register('JEVConfig', JEV_ADMINPATH . "libraries/config.php");
JLoader::register('JevRegistry', JEV_PATH . "libraries/registry.php");

JLoader::register('SaveIcalEvent', JEV_ADMINLIBS . "saveIcalEvent.php");

JLoader::register('JEVHelper', JEV_PATH . "libraries/helper.php");
JLoader::register('JevModuleHelper', JEV_PATH . "libraries/jevmodulehelper.php");
JLoader::register('JevHtmlBootstrap', JEV_PATH . "libraries/bootstrap.php");
JLoader::register('JevModal', JPATH_LIBRARIES . "/jevents/jevmodal/jevmodal.php");

// TODO replace with Date
JLoader::register('JEventDate', JEV_PATH . "libraries/jeventdate.php");
JLoader::register('JevDate', JEV_PATH . "libraries/jevdate.php");
JLoader::register('JEventsHTML', JEV_PATH . "libraries/jeventshtml.php");

JLoader::register('JEventsVersion', JEV_ADMINPATH . "libraries/version.php");

JLoader::register('catLegend', JEV_PATH . "libraries/catLegend.php");

JLoader::register('JEventsDBModel', JEV_PATH . "libraries/dbmodel.php");
JLoader::register('JEventsDataModel', JEV_PATH . "libraries/datamodel.php");

JLoader::register('iCalImport', JEV_PATH . "libraries/iCalImport.php");
JLoader::register('iCalRepetition', JEV_PATH . "libraries/iCalRepetition.php");
JLoader::register('iCalException', JEV_PATH . "libraries/iCalException.php");
JLoader::register('iCalRRule', JEV_PATH . "libraries/iCalRRule.php");
JLoader::register('iCalEvent', JEV_PATH . "libraries/iCalEvent.php");
JLoader::register('iCalEventDetail', JEV_PATH . "libraries/iCalEventDetail.php");
JLoader::register('iCalICSFile', JEV_PATH . "libraries/iCalICSFile.php");
JLoader::register('CsvToiCal', JEV_PATH . "libraries/csvToiCal.php");

JLoader::register('jEventCal', JEV_PATH . "libraries/jeventcal.php");
JLoader::register('jIcal', JEV_PATH . "libraries/jical.php");
JLoader::register('jIcalEventDB', JEV_PATH . "libraries/jicaleventdb.php");
JLoader::register('jIcalEventRepeat', JEV_PATH . "libraries/jicaleventrepeat.php");

JLoader::register('jevFilterProcessing', JEV_PATH . "libraries/filters.php");

JLoader::register('JEventsAbstractView', JEV_ADMINPATH . "views/abstract/abstract.php");
JLoader::register('JEventsDefaultView', JEV_PATH . "views/default/abstract/abstract.php");

// joomla 3.0
JLoader::register('JToolbarButtonJev', JEV_ADMINPATH . "libraries/jevtoolbarbuttons.php");
JLoader::register('JToolbarButtonJevlink', JEV_ADMINPATH . "libraries/jevtoolbarbuttons.php");
JLoader::register('JToolbarButtonJevconfirm', JEV_ADMINPATH . "libraries/jevtoolbarbuttons.php");


// TODO retire this sometime?
JLoader::register('JEV_CommonFunctions', JEV_PATH . "libraries/commonfunctions.php");

jimport('joomla.filesystem.folder');

