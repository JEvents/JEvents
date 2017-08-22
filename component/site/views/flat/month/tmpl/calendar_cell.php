<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: calendar_cell.php 2679 2011-10-03 08:52:57Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2017 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\String\StringHelper;

include_once(JEV_VIEWS . "/default/month/tmpl/calendar_cell.php");

class EventCalendarCell_flat  extends EventCalendarCell_default {

	function calendarCell(&$currentDay,$year,$month,$i, $slot=""){

		$cfg = JEVConfig::getInstance();

		$Itemid = JEVHelper::getItemid();

		$event_day = $this->event->dup();
		$event_month = $this->event->mup();

		$id = $this->event->id();

		// this file controls the events component month calendar display cell output.  It is separated from the
		// showCalendar function in the events.php file to allow users to customize this portion of the code easier.
		// The event information to be displayed within a month day on the calendar can be modified, as well as any
		// overlay window information printed with a javascript mouseover event.  Each event prints as a separate table
		// row with a single column, within the month table's cell.

		// define start and end
		$cellStart	= '<div class="month_cell_st"';
		$cellStyle	= '';
		$cellEnd		= '</div>' . "\n";

		// add the event color as the column background color
		$cellStyle .= 'border-bottom:1px solid ' . $this->event->bgcolor() . ';border-left:3px solid ' . $this->event->bgcolor() . ';color:'.$this->event->fgcolor() . ';' ;

		// MSIE ignores "inherit" color for links - stupid Microsoft!!! :-)
                // So lets set a defined color as we have defined background color :)
		$linkStyle = 'style="color:#474747;"';

		// The title is printed as a link to the event's detail page
		$link = $this->event->viewDetailLink($year,$month,$currentDay['d0'],false);
		$link = JRoute::_($link.$this->_datamodel->getCatidsOutLink());

		$title          = $this->event->title();
		
		// [mic] if title is too long, cut 'em for display
		$tmpTitle = $title;
		// set truncated title
		if (!isset($this->event->truncatedtitle)){
			if( JString::strlen( $title ) >= $cfg->get('com_calCutTitle',50)){
				$tmpTitle = JString::substr( $title, 0, $cfg->get('com_calCutTitle',50) ) . ' ...';
			}
			$tmpTitle = JEventsHTML::special($tmpTitle);			
			$this->event->truncatedtitle = $tmpTitle;
		}
		else {
			$tmpTitle = $this->event->truncatedtitle ;
		}

		// [new mic] if amount of displaing events greater than defined, show only a scmall coloured icon
		// instead of full text - the image could also be "recurring dependig", which means
		// for each kind of event (one day, multi day, last day) another icon
		// in this case the dfinition must moved down to be more flexible!

		// [tstahl] add a graphic symbol for all day events?
		$tmp_start_time = (($this->start_time == $this->stop_time && !$this->event->noendtime()) || $this->event->alldayevent()) ? '' : $this->start_time;

		$templatedcell = false;
		if( $currentDay['countDisplay'] < $cfg->get('com_calMaxDisplay',5)){
			ob_start();
			$templatedcell = $this->loadedFromTemplate('month.calendar_cell', $this->event, 0);
			$res = ob_get_clean();
			if ($templatedcell){
				$templatedcell = $res;
			}			
			else {
				if ($this->_view){
					$this->_view->assignRef("link",$link);
					$this->_view->assignRef("linkStyle",$linkStyle);
					$this->_view->assignRef("tmp_start_time",$tmp_start_time);
					$this->_view->assignRef("tmpTitle",$tmpTitle);
				}
				$title_event_link = $this->loadOverride("cellcontent");
				// allow fallback to old method
				if ($title_event_link==""){
					$title_event_link = '<a class="cal_titlelink" href="' . $link . '">'
					. ( $cfg->get('com_calDisplayStarttime') ? $tmp_start_time : '' ) . ' ' . $tmpTitle . '</a>' . "\n";
				}
				$cellStyle .= ' width:100%;';
			}
		}else{
			$eventIMG	= '<img align="left" style="border:1px solid white;" src="' . JURI::root()
			. 'components/'.JEV_COM_COMPONENT.'/images/event.png" height="12" width="8" alt=""' . ' />';

			$title_event_link = '<a class="cal_titlelink"  href="' . $link . '">' . $eventIMG . '</a>' . "\n";
			$cellStyle .= ' float:left;width:10px;';
		}
		
		$cellString	= '';
		// allow template overrides for cell popups
		// only try override if we have a view reference
		if ($this->_view){
			$this->_view->assignRef("ecc",$this);
			$this->_view->assignRef("cellDate",$currentDay["cellDate"]);
		}

		if( $cfg->get("com_enableToolTip",1)) {
			if ($cfg->get("tooltiptype",'joomla')=='overlib'){
				$tooltip = $this->loadOverride("overlib");
				// allow fallback to old method
				if ($tooltip==""){
					$tooltip=$this->calendarCell_popup($currentDay["cellDate"]);
				}
				$cellString .= $tooltip;
			}
			else {
				// TT background
				if( $cfg->get('com_calTTBackground',1) == '1' ){
					$bground =  $this->event->bgcolor();
					$fground =  $this->event->fgcolor();
				}
				else {
					$bground =  "#000000";
					$fground =  "#474747";

				}

				JevHtmlBootstrap::popover('.hasjevtip' , array("trigger"=>"hover focus", "placement"=>"top", "container"=>"#jevents_body", "delay"=> array( "show"=> 150, "hide"=> 150 )));
				//$toolTipArray = array('className' => 'jevtip');
				//JHTML::_('behavior.tooltip', '.hasjevtip', $toolTipArray);

				$tooltip = $this->loadOverride("tooltip");
				// allow fallback to old method
				if ($tooltip==""){
					$tooltip = $this->calendarCell_tooltip($currentDay["cellDate"]);
				}
				$tooltip = $this->correctTooltipLanguage($tooltip);

				if (strpos($tooltip,"templated")===0 ) {
					$cellString = JString::substr($tooltip,9);
					$dom = new DOMDocument();
                                        // see http://php.net/manual/en/domdocument.savehtml.php cathexis dot de ¶
                                        $dom->loadHTML('<html><head><meta content="text/html; charset=utf-8" http-equiv="Content-Type"></head><body>'.$cellString.'</body>');

					$classname = 'jevtt_title';
					$finder = new DomXPath($dom);
					$nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");

					if ($nodes->length){
						foreach ($nodes as $node){
							$title = $dom->saveHTML($node);
							$node->parentNode->removeChild($node);
						}
						$body = $dom->getElementsByTagName('body')->item(0);
						$cellString= '';
						$children = $body->childNodes;
						foreach ($children as $child) {
							$cellString .= $child->ownerDocument->saveXML( $child );
						}
					}
					else {
						$title = $cellString;
						$cellString = "";
					}
				}
				else {
					$cellString .= '<div class="jevtt_text" >'.$tooltip.'</div>';
					$title = '<div class="jevtt_title" style = "color:'.$fground.';background-color:'.$bground.'">'.$this->title.'</div>';
				}
				
				if ($templatedcell){
					$templatedcell = str_replace("[[TOOLTIP]]", htmlspecialchars($title.$cellString,ENT_QUOTES), $templatedcell);
					$templatedcell = str_replace("[[TOOLTIPTITLE]]", htmlspecialchars($title,ENT_QUOTES), $templatedcell);
					$templatedcell = str_replace("[[TOOLTIPCONTENT]]", htmlspecialchars($cellString,ENT_QUOTES), $templatedcell);
					$time = $cfg->get('com_calDisplayStarttime')?$tmp_start_time:"";
					$templatedcell = str_replace("[[EVTTIME]]", $time, $templatedcell);
					return  $templatedcell;
				}

				$html =  $cellStart . ' style="' . $cellStyle . '">' . $this->tooltip( $title , $cellString, $title_event_link) . $cellEnd;

				return $html;
			}

		}
		if ($templatedcell)
		{
			$templatedcell = str_replace("[[TOOLTIP]]", htmlspecialchars($title.$cellString,ENT_QUOTES), $templatedcell);
			$templatedcell = str_replace("[[TOOLTIPTITLE]]", htmlspecialchars($title,ENT_QUOTES), $templatedcell);
			$templatedcell = str_replace("[[TOOLTIPCONTENT]]", htmlspecialchars($cellString,ENT_QUOTES), $templatedcell);
			$time = $cfg->get('com_calDisplayStarttime') ? $tmp_start_time : "";
			$templatedcell = str_replace("[[EVTTIME]]", $time, $templatedcell);
			return $templatedcell;
		}

		// return the whole thing
		return $cellStart . ' style="' . $cellStyle . '" ' . $cellString . ">\n" . $title_event_link . $cellEnd;
	}
/*
	function tooltip($tooltip,  $link)
	{
		//$tooltip	= addslashes(htmlspecialchars($tooltip));
		$tooltip	= htmlspecialchars($tooltip,ENT_QUOTES);

		$tip = '<span class="editlinktip hasjevtip" title="'.$tooltip.'" rel=" ">'.$link.'</span>';

		return $tip;
	}

	function loadOverride($tpl){
		$tooltip = "";
		// only try override if we have a view reference
		if ($this->_view){

			//create the template file name based on the layout
			$file = $this->_view->getLayout().'_'.$tpl;
			// clean the file name
			$file = preg_replace('/[^A-Z0-9_\.-]/i', '', $file);

			// load the template script
			jimport('joomla.filesystem.path');
			$filetofind	= strtolower($file).".php";
			$paths = $this->_view->get("_path");
			if ( JPath::find($paths['template'], $filetofind)){
				$tooltip = $this->_view->loadTemplate($tpl);
			}
		}
		return $tooltip;
	}
*/
	
	protected function correctTooltipLanguage($tip){
		return str_replace(JText::_("JEV_FIRST_DAY_OF_MULTIEVENT"), JText::_("JEV_MULTIDAY_EVENT"), $tip);
	}
	
}
