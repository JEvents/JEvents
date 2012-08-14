<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: legend.php 1031 2010-07-05 14:43:07Z geraintedwards $
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

$include = str_replace('modules/mod_jevents_legend/tmpl/jomsocial',"modules/mod_jevents_legend/tmpl/$parentview",dirname(__FILE__))."/".basename(__FILE__);
// include the core parent class
include_once($include);

switch ($parentview) {
	case "extplus" :
		class JomsocialModLegendView extends ExtplusModLegendView {

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
		class JomsocialModLegendView extends RuthinModLegendView {

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
		class JomsocialModLegendView extends IconicModLegendView {

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
