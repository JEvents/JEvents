<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: Startdate.php 1976 2011-04-27 15:54:31Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2018 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined('_VALID_MOS') or defined('_JEXEC') or die( 'No Direct Access' );
JLoader::register('JevJoomlaVersion',JPATH_ADMINISTRATOR."/components/com_jevents/libraries/version.php");

use Joomla\String\StringHelper;

// Event repeat startdate fitler
class jevStartdateFilter extends jevFilter
{

	var $dmap="";
	var $_onorbefore = false;
	var $_date = "";

	function __construct($tablename, $filterfield, $isstring=true){
		$this->fieldset=true;

		$this->valueNum=3;
		$this->filterNullValue=0;
		$this->filterNullValues[0]=0; // n/a, before, after
		$this->filterNullValues[1]=""; // the date
		$this->filterNullValues[2]=0; // true means the form is submitted
	
		$this->filterType="startdate";
		$this->filterLabel="";
		$this->dmap = "rpt";
		parent::__construct($tablename,$filterfield, true);
		
		// This filter is special and always remembers for logged in users
		if (JFactory::getUser()->id>0){
			$this->filter_value = JFactory::getApplication()->getUserStateFromRequest( $this->filterType.'_fv_ses', $this->filterType.'_fv', $this->filterNullValue );
			for ($v=0;$v<$this->valueNum;$v++){
				$this->filter_values[$v] = JFactory::getApplication()->getUserStateFromRequest( $this->filterType.'_fvs_ses'.$v, $this->filterType.'_fvs'.$v,$this->filterNullValues[$v] );
			}
		}

		$this->_date = $this->filter_values[1];
		$this->_onorbefore = $this->filter_values[0];

	}

	function _createFilter($prefix=""){
		if (!$this->filterField ) return "";
		// first time visit
		if (isset($this->filter_values[2]) && $this->filter_values[2]==0) {
			$this->filter_values = array();
			$this->filter_values[0]=1;
			// default scenario is only events starting after 2 weeeks ago			
			$fulldate = date( 'Y-m-d H:i:s',JevDate::strtotime("-2 weeks"));
			$this->filter_values[1]=JString::substr($fulldate,0,10);
			$this->filter_values[2]=1;
			return  $this->dmap.".startrepeat>='$fulldate'";
		}
		else if ($this->filter_values[0]==0){
			$this->filter_values[1]="";
			$this->_date = $this->filter_values[1];
		}
		else if ($this->filter_values[0]==-1 && $this->filter_values[1]==""){
			$fulldate = date( 'Y-m-d H:i:s',JevDate::strtotime("+2 weeks"));
			$this->filter_values[1]=JString::substr($fulldate,0,10);
			$this->_date = $this->filter_values[1];
		}
		else if ($this->filter_values[0]==1 && $this->filter_values[1]==""){
			$fulldate = date( 'Y-m-d H:i:s',JevDate::strtotime("-2 weeks"));
			$this->filter_values[1]=JString::substr($fulldate,0,10);
			$this->_date = $this->filter_values[1];
		}
		$filter="";

		if ($this->_date!="" && $this->_onorbefore!=0){
			$date = JevDate::strtotime($this->_date);
			$fulldate = date( 'Y-m-d H:i:s',$date);
			if ($this->_onorbefore>0){
				$date = $this->dmap.".startrepeat>='$fulldate'";
			}
			else {
				$date = $this->dmap.".startrepeat<'$fulldate'";
			}
		}
		else {
			$date = "";
		}
		$filter = $date;

		return $filter;
	}

	function _createfilterHTML(){

		if (!$this->filterField) return "";

		// only works on admin list events pages
		if (JRequest::getCmd("jevtask")!="admin.listevents"){
			$filterList=array();
			$filterList["title"]="";

			$filterList["html"] = "";
			return $filterList;
		}
		
		$filterList=array();
		$filterList["title"]=JText::_( 'WITH_INSTANCES' );

		$filterList["html"] = "";

		$options = array();
		$options[] = JHTML::_('select.option', '0',JText::_( 'WHEN' ) );
		$options[] = JHTML::_('select.option', '1',JText::_('On_or_after') );
		$options[] = JHTML::_('select.option', '-1',JText::_( 'BEFORE' ) );
		$filterList["html"] .= JHTML::_('select.genericlist', $options, $this->filterType.'_fvs0', 'onchange="form.submit()" class="inputbox" size="1" ', 'value', 'text', $this->filter_values[0] );

		//$filterList["html"] .=  JHTML::calendar($this->filter_values[1],$this->filterType.'_fvs1', $this->filterType.'_fvs1', '%Y-%m-%d',array('size'=>'12','maxlength'=>'10','onchange'=>'form.submit()'));
		
			$params = JComponentHelper::getParams( JEV_COM_COMPONENT );
			$minyear = JEVHelper::getMinYear();
			$maxyear = JEVHelper::getMaxYear();
			$document = JFactory::getDocument();
			
			$calendar = 'calendar14.js' ;
		
			JEVHelper::script($calendar, "components/".JEV_COM_COMPONENT."/assets/js/",true); 
			JEVHelper::stylesheet("dashboard.css",  "components/".JEV_COM_COMPONENT."/assets/css/",true);  
			$document->addScriptDeclaration('window.addEvent(\'domready\', function() {
				new NewCalendar({ '.$this->filterType.'_fvs1 :  "Y-m-d"},{
					direction:0, 
					classes: ["dashboard"],
					draggable:true,
					navigation:2,
					tweak:{x:0,y:-75},
					offset:1,
					range:{min:'.$minyear.',max:'.$maxyear.'},
					months:["'.JText::_("JEV_JANUARY").'",
					"'.JText::_("JEV_FEBRUARY").'",
					"'.JText::_("JEV_MARCH").'",
					"'.JText::_("JEV_APRIL").'",
					"'.JText::_("JEV_MAY").'",
					"'.JText::_("JEV_JUNE").'",
					"'.JText::_("JEV_JULY").'",
					"'.JText::_("JEV_AUGUST").'",
					"'.JText::_("JEV_SEPTEMBER").'",
					"'.JText::_("JEV_OCTOBER").'",
					"'.JText::_("JEV_NOVEMBER").'",
					"'.JText::_("JEV_DECEMBER").'"
					],
					days :["'.JText::_("JEV_SUNDAY").'",
					"'.JText::_("JEV_MONDAY").'",
					"'.JText::_("JEV_TUESDAY").'",
					"'.JText::_("JEV_WEDNESDAY").'",
					"'.JText::_("JEV_THURSDAY").'",
					"'.JText::_("JEV_FRIDAY").'",
					"'.JText::_("JEV_SATURDAY").'"
					], 
					onHideComplete : function () { $("'.$this->filterType.'_fvs1").form.submit()},					
				});
			});');


			$filterList["html"] .=  '<input type="text" name="'.$this->filterType.'_fvs1" id="'.$this->filterType.'_fvs1" value="'.$this->filter_values[1].'" maxlength="10" size="12"  />';		

		$filterList["html"] .= "<input type='hidden' name='".$this->filterType."_fvs2' value='1'/>";
		
		return $filterList;
		
		
	}
}
