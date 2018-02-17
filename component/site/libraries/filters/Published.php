<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: Published.php 3549 2012-04-20 09:26:21Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2018 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// ensure this file is being included by a parent file
defined('_JEXEC') or die( 'Direct Access to this location is not allowed.' );



/**
 * Filters events to restrict events for administration - used for administration of events in the frontend
 */

class jevPublishedFilter extends jevFilter
{
	var $label="";
	var $yesLabel = "";
	var $noLabel = "";
	var $isEventAdmin = false;
	const filterType = "published";

	function __construct($tablename, $filterfield, $isstring=true,$yesLabel="Jev_Yes", $noLabel="Jev_No"){
		$jinput = JFactory::getApplication()->input;

		$this->filterType=self::filterType;
		$task = $jinput->get('view', '') . '.' . $jinput->get('layout', '');
		if ($task == "admin.listevents") { $default_filter = "-1"; } else { $default_filter = "0";}
		
		$this->filterNullValue= $default_filter;
		$this->allLabel = JText::_( 'ALL' );
		$this->yesLabel = JText::_($yesLabel);
		$this->noLabel =  JText::_($noLabel);
		$this->filterLabel = JText::_("Show_Unpublished_Events");
		
		// this is a special filter - we always want memory here since only used in frontend management
		
		$this->filter_value = JFactory::getApplication()->getUserStateFromRequest( $this->filterType.'_fv_ses', $this->filterType.'_fv', $this->filterNullValue );		
		$jinput->set($this->filterType.'_fv', $this->filter_value);
		
		parent::__construct($tablename, "state", $isstring);
		
		// event creators can look at their own unpublished events
		if (!JEVHelper::isEventCreator()){
			$this->filter_value = $this->filterNullValue;
		}
	}

	function _createFilter($prefix=""){
		if (!$this->filterField ) return "ev.state=1";	

		// The default is only to show published events
		if ($this->filter_value==0) return "ev.state=1";	
		
		// only show published events to non-logged in users
		$user = JFactory::getUser();
		if ($user->get('id')==0){
			return "ev.state=1";
		}
		
		if (JEVHelper::isEventPublisher(true) || JEVHelper::isEventEditor()){
			if ($this->filter_value==-1) return "ev.state<>-1";
			return "ev.state=0";
		}
		else if  (JEVHelper::isEventCreator()){
			$user = JFactory::getUser();
			if ($this->filter_value==-1) return "(ev.state=1 OR ev.created_by=".$user->id.")";
			return "ev.state=0 && ev.created_by=".$user->id;
		}
		
		return "ev.state=1";	
	}

	function _createfilterHTML(){
		$filterList=array();
		$filterList["title"] = $this->filterLabel;
		$options = array();
		$options[] = JHTML::_('select.option', "-1", $this->allLabel,"value","yesno");
		$options[] = JHTML::_('select.option', "0", $this->noLabel,"value","yesno");
		$options[] = JHTML::_('select.option',  "1", $this->yesLabel,"value","yesno");
		$filterList["html"] = JHTML::_('select.genericlist',$options, $this->filterType.'_fv', 'class="inputbox" size="1" onchange="form.submit();"', 'value', 'yesno', $this->filter_value );
		return $filterList;
	}

}
