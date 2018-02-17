<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: mod.defines.php 3059 2011-12-01 12:25:04Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2018 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined('_JEXEC') or die( 'No Direct Access' );

if (!defined("JEV_COM_COMPONENT")){
	define("JEV_COM_COMPONENT","com_jevents");
	define("JEV_COMPONENT",str_replace("com_","",JEV_COM_COMPONENT));
}

if (!defined("JEV_LIBS")){
	define("JEV_ADMINPATH",JPATH_ADMINISTRATOR."/components/".JEV_COM_COMPONENT."/");
	define("JEV_PATH",JPATH_SITE."/components/".JEV_COM_COMPONENT."/");
	define("JEV_VIEWS",JEV_PATH."views");
	define("JEV_LIBS",JEV_PATH."libraries");
	define("JEV_ABSTRACTEMPLATES",JEV_VIEWS."/abstract/tmpl/");
	define("JEV_ADMINLIBS",JEV_ADMINPATH."libraries/");
}
	JLoader::register('JSite' , JPATH_SITE.'/includes/application.php');
	JLoader::register('JevRegistry',JEV_PATH."/libraries/registry.php");


	JLoader::register('JEVConfig',JEV_ADMINPATH."/libraries/config.php");
	
	JLoader::register('JEVHelper',JEV_PATH."/libraries/helper.php");
	JLoader::register('JevModuleHelper',JEV_PATH."/libraries/jevmodulehelper.php"); // RSH Required registration for class!

	// TODO replace with JDate
	JLoader::register('JEventDate',JEV_PATH."/libraries/jeventdate.php");
	JLoader::register('JevDate',JEV_PATH."/libraries/jevdate.php");
	JLoader::register('JEventsHTML',JEV_PATH."/libraries/jeventshtml.php");

	JLoader::register('JEventsVersion',JEV_ADMINPATH."/libraries/version.php");
	JLoader::register('JevJoomlaVersion',JPATH_ADMINISTRATOR."/components/com_jevents/libraries/version.php");
	
	JLoader::register('catLegend',JEV_PATH."/libraries/catLegend.php");
	
	JLoader::register('JEventsDBModel',JEV_PATH."/libraries/dbmodel.php");
	JLoader::register('JEventsDataModel',JEV_PATH."/libraries/datamodel.php");

	JLoader::register('jEventCal',JEV_PATH."/libraries/jeventcal.php");
	JLoader::register('jIcal',JEV_PATH."/libraries/jical.php");
	JLoader::register('jIcalEventDB',JEV_PATH."/libraries/jicaleventdb.php");
	JLoader::register('jIcalEventRepeat',JEV_PATH."/libraries/jicaleventrepeat.php");
	
	JLoader::register('JEventsAbstractView',JEV_ADMINPATH."/views/abstract/abstract.php");
	
	JLoader::register('jevFilterProcessing',JEV_PATH."/libraries/filters.php");
	
	// TODO retire this sometime?
	JLoader::register('JEV_CommonFunctions',JEV_PATH."/libraries/commonfunctions.php");
