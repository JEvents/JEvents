<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: latest.php 1032 2010-07-05 14:43:17Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

// include the parent view!
$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
$parentview = $params->get("jsparentview","default");

$include = str_replace('modules/mod_jevents_latest/tmpl/jomsocial',"modules/mod_jevents_latest/tmpl/$parentview",dirname(__FILE__))."/".basename(__FILE__);
// include the core parent class
include_once($include);

switch ($parentview) {
	case "extplus" :
		class JomsocialModLatestView extends ExtplusModLatestView {

			function __construct($params, $modid){
				$this->jevlayout = "extplus";
				$result =  parent::__construct($params, $modid);
				return $result;
			}
			
			function  getTheme() {
				return $this->jevlayout;
			}
			
		}
		break;
	case "ruthin" :
		class JomsocialModLatestView extends RuthinModLatestView {

			function __construct($params, $modid){
				$this->jevlayout = "ruthin";
				$result =  parent::__construct($params, $modid);
				return $result;
			}
			
			function  getTheme() {
				return $this->jevlayout;
			}
			
		}
		break;
	case "iconic" :
		class JomsocialModLatestView extends IconicModLatestView {

			function __construct($params, $modid){
				$this->jevlayout = "iconic";
				$result =  parent::__construct($params, $modid);
				return $result;
			}
			
			function  getTheme() {
				return $this->jevlayout;
			}
			
		}
		break;
}
