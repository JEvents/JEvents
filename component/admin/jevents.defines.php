<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: jevents.defines.php 3091 2011-12-11 10:01:05Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2009 GWE Systems Ltd
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
	define("JEV_LIBS",JEV_ADMINPATH."libraries/");
	define("JEV_ADMINLIBS",JEV_ADMINPATH."libraries/");
	define("JEV_HELPERS",JEV_ADMINPATH."helpers/");
	define("JEV_CONFIG",JEV_ADMINPATH."config/");
	define("JEV_FILTERS",JEV_ADMINPATH."filters/");
	define("JEV_LAYOUTS",JEV_ADMINPATH."layouts/");
	define("JEV_VIEWS",JEV_ADMINPATH."views");
}
	JLoader::register('JSite' , JPATH_SITE.'/includes/application.php');

	JLoader::register('JEVConfig',JEV_LIBS."config.php");

	JLoader::register('SaveIcalEvent',JEV_LIBS."saveIcalEvent.php");

	JLoader::register('JEventsVersion',JEV_LIBS."version.php");
	JLoader::register('JEventsDBModel',JEV_PATH."libraries/dbmodel.php");
	JLoader::register('JEventsDataModel',JEV_PATH."libraries/datamodel.php");
	
	JLoader::register('JEVHelper',JEV_PATH."libraries/helper.php");
	JLoader::register('JevModuleHelper',JEV_PATH."/libraries/jevmodulehelper.php"); 
	
	JLoader::register('JEventsAbstractView',JEV_VIEWS."/abstract/abstract.php");
	
	JLoader::register('jEventCal',JEV_PATH."libraries/jeventcal.php");
	JLoader::register('jIcal',JEV_PATH."libraries/jical.php");
	JLoader::register('jIcalEventDB',JEV_PATH."libraries/jicaleventdb.php");
	JLoader::register('jIcalEventRepeat',JEV_PATH."libraries/jicaleventrepeat.php");

	JLoader::register('iCalImport',JEV_PATH."libraries/iCalImport.php");
	JLoader::register('iCalRepetition',JEV_PATH."libraries/iCalRepetition.php");
	JLoader::register('iCalException',JEV_PATH."libraries/iCalException.php");
	JLoader::register('iCalRRule',JEV_PATH."libraries/iCalRRule.php");
	JLoader::register('iCalEvent',JEV_PATH."libraries/iCalEvent.php");
	JLoader::register('iCalEventDetail',JEV_PATH."libraries/iCalEventDetail.php");
	JLoader::register('iCalICSFile',JEV_PATH."libraries/iCalICSFile.php");
	JLoader::register('CsvToiCal',JEV_PATH."/libraries/csvToiCal.php");
	
	// TODO replace with JDate
	JLoader::register('JEventDate',JEV_PATH."libraries/jeventdate.php");
	JLoader::register('JevDate',JEV_PATH."/libraries/jevdate.php");
	JLoader::register('JEventsHTML',JEV_PATH."libraries/jeventshtml.php");

	// TODO retire this sometime? - check usage by session registration code first
	JLoader::register('JEV_CommonFunctions',JEV_PATH."libraries/commonfunctions.php");
	


