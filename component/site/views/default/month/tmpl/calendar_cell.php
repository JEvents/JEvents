<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: calendar_cell.php 2679 2011-10-03 08:52:57Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2018 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\String\StringHelper;

class EventCalendarCell_default  extends JEventsDefaultView {
	protected $_datamodel = null;
	protected $_view = null;

	function __construct($event, $datamodel, $view=false){
		$cfg = JEVConfig::getInstance();
		$this->event = $event;
		$this->_datamodel = $datamodel;
		$this->_view = $view;

		$this->start_publish  = $this->event->getUnixStartDate();
		$this->stop_publish  = $this->event->getUnixEndDate();
		$this->title          = $this->event->title();

		// On mouse over date formats
		$this->start_date	= JEventsHTML::getDateFormat( $this->event->yup(), $this->event->mup(), $this->event->dup(), 0 );
		//$this->start_time = $this->event->startTime()	;
		$this->start_time = JEVHelper::getTime($this->event->getUnixStartTime(),$this->event->hup(),$this->event->minup());

		$this->stop_date	= JEventsHTML::getDateFormat(  $this->event->ydn(), $this->event->mdn(), $this->event->ddn(), 0 );
		//$this->stop_time = $this->event->endTime()	;		
		$this->stop_time  = JEVHelper::getTime($this->event->getUnixEndTime(),$this->event->hdn(),$this->event->mindn());
		
		$this->stop_time_midnightFix = $this->stop_time ;
		$this->stop_date_midnightFix = $this->stop_date ;
		if ($this->event->sdn() == 59 && $this->event->mindn()==59){
			$this->stop_time_midnightFix = JEVHelper::getTime($this->event->getUnixEndTime()+1,0,0);
			$this->stop_date_midnightFix = JEventsHTML::getDateFormat(  $this->event->ydn(), $this->event->mdn(), $this->event->ddn()+1, 0 );
		}
		
		$this->jevlayout=isset($view->jevlayout) ? $view->jevlayout : "default";
		
		$this->addHelperPath(JEV_VIEWS."/default/helpers");
		$this->addHelperPath( JPATH_BASE.'/'.'templates'.'/'.JFactory::getApplication()->getTemplate().'/'.'html'.'/'.JEV_COM_COMPONENT.'/'."helpers");

		// attach data model
		$reg = JevRegistry::getInstance("jevents");
		$this->datamodel  =  $reg->getReference("jevents.datamodel");

	}
	
	function calendarCell_popup($cellDate){
		$cfg = JEVConfig::getInstance();

		$publish_inform_title 	= htmlspecialchars( $this->title );
		$publish_inform_overlay	= '';
		$cellString="";
		// The one overlay popup window defined for multi-day events.  Any number of different overlay windows
		// can be defined here and used according to the event's repeat type, length, whatever.  Note that the
		// definition of the overlib function call arguments is ( html_window_contents, extra optional paramenters ... )
		// 'extra parameters' includes things like window positioning, display delays, window caption, etc.
		// Documentation on the javascript overlib library can be found at: http://www.bosrup.com/web/overlib/
		// or here for additional plugins (like shadow): http://overlib.boughner.us/ [mic]

		// check this speeds up that thing [mic]		// TODO if $publish_inform_title  is blank we get problems
		$tmp_time_info = '';
		if( $publish_inform_title ){
			if( $this->stop_publish == $this->start_publish ){
				if ($this->event->noendtime()){
					$tmp_time_info = '<br />' . $this->start_time;
				}
				else if ($this->event->alldayevent()){
					$tmp_time_info = "";
				}
				else if($this->start_time != $this->stop_time ){
					$tmp_time_info = '<br />' . $this->start_time . ' - ' . $this->stop_time_midnightFix;
				}
				else {
					$tmp_time_info = '<br />' . $this->start_time;
				}

				$publish_inform_overlay = '<table class="w100 b0">'
				. '<tr><td nowrap=&quot;nowrap&quot;>' . $this->start_date
				. $tmp_time_info
				;
			} else {
				if ($this->event->noendtime()){
					$tmp_time_info = '<br /><b>' . JText::_('JEV_TIME') . ':&nbsp;</b>' . $this->start_time;
				}
				else if($this->start_time != $this->stop_time && !$this->event->alldayevent()){
					$tmp_time_info = '<br /><b>' . JText::_('JEV_TIME') . ':&nbsp;</b>' . $this->start_time . '&nbsp;-&nbsp;' . $this->stop_time_midnightFix;
				}
				$publish_inform_overlay = '<table class="w100 b0 h100">'
				. '<tr><td><b>' . JText::_('JEV_FROM') . ':&nbsp;</b>' . $this->start_date . '&nbsp;'
				. '<br /><b>' . JText::_('JEV_TO') . ':&nbsp;</b>' . $this->stop_date
				. $tmp_time_info
				;
			}
		}

		// Event Repeat Type Qualifier and Day Within Event Quailfiers:
		// the if statements below basically will print different information for the event
		// depending upon whether it is the start/stop day, repeat events type, or some date in between the
		// start and the stop dates of a multi-day event.  This behavior can be modified at will here.
		// Currently, an overlay window will only display on a mouseover if the event is a multi-day
		// event (ie. every day repeat type) AND the month cell is a day WITHIN the event day range BUT NOT
		// the start and stop days.  The overlay window displays the start and stop publish dates.  Different
		// overlay windows can be displayed for the different states below by simply defining a new overlay
		// window definition variable similar to the $publish_inform_overlay variable above and using it in the
		// statements below.  Another possibility here is to control the max. length of any string used within the
		// month cell to avoid calendar formatting issues.  Any string that exceeds this will get an overlay window
		// in order to display the full length/width of the month cell.

		// Note that we want multi-day events to display a titlelink for the first day only, but a popup for every day
		// Fix this.

		if ($this->event->alldayevent() && $this->start_date==$this->stop_date){
			// just print the title
			$cellString = $publish_inform_overlay
			. '<br /><span class="fwb">' . ($this->event->isRepeat()?JText::_("JEV_REPEATING_EVENT"):JText::_('JEV_FIRST_SINGLE_DAY_EVENT') ). '</span>';
		}
		else if(( $cellDate == $this->stop_publish ) && ( $this->stop_publish == $this->start_publish )) {
			// single day event
			// just print the title
			$cellString = $publish_inform_overlay
			. '<br /><span class="fwb">' . ($this->event->isRepeat()?JText::_("JEV_REPEATING_EVENT"):JText::_('JEV_FIRST_SINGLE_DAY_EVENT') ) . '</span>';
		}elseif( $cellDate == $this->start_publish ){
			// first day of a multi-day event
			// just print the title
			$cellString = $publish_inform_overlay
			. '<br /><span class="fwb">' . JText::_('JEV_FIRST_DAY_OF_MULTIEVENT') . '</span>';
		}elseif( $cellDate == $this->stop_publish ){
			// last day of a multi-day event
			// enable an overlib popup
			$cellString = $publish_inform_overlay
			. '<br /><span class="fwb">' . JText::_('JEV_LAST_DAY_OF_MULTIEVENT') . '</span>';
		}elseif(( $cellDate < $this->stop_publish ) && ( $cellDate > $this->start_publish ) ) {
			// middle day of a multi-day event
			// enable the display of an overlib popup describing publish date
			$cellString = $publish_inform_overlay
			. '<br /><span class="fwb">' . JText::_('JEV_MULTIDAY_EVENT') . '</span>';
		}else{
			// this should never happen, but is here just in case...
			$cellString =  $publish_inform_overlay.'<br /><small><div class="probs_check_ev">Problems - check event!</div></small>';
			$title_event_link = "<div class='probs_check_ev'>Problems - check event!</div>";
			$cellStart   = '';
			$cellClass   = '';
			$cellEnd     = '';
		}

		/**
 * defining the design of the tooltip
 * AUTOSTATUSCAP 	displays title in browsers statusbar (only IE)
 * if no vlaus are defined, the overlib standard values are used
 * TT backgrund	bool
 * TT posX		string	left, center, right (right = standard)
 * TT posY		string	above, below (below = standard)
 * shadow		bool
 * shadox posX	bool (standard = right)
 * shadow posY	bool (standard = below)
 * FGCOLOR		string	set here fix (could be also defined in config - later)
 * CAPCOLOR		string	set here fix (could be also defined in config - later)
 **/

		// set standard values
		$ttBGround 		= '';
		$ttXPos 		= '';
		$ttYPos 		= '';
		$ttShadow 		= '';
		$ttShadowColor  = '';
		$ttShadowX      = '';
		$ttShadowY      = '';

		// TT background
		if( $cfg->get('com_calTTBackground',1) == '1' ){
			$ttBGround = ' BGCOLOR, \'' . $this->event->bgcolor() . '\',';
			$ttFGround = ' CAPCOLOR, \'' . $this->event->fgcolor() . '\',';
		}
		else $ttFGround = ' CAPCOLOR, \'#000000\',';

		// TT xpos
		if( $cfg->get('com_calTTPosX') == 'CENTER' ){
			$ttXPos = ' CENTER,';
		}elseif( $cfg->get('com_calTTPosX') == 'LEFT' ){
			$ttXPos = ' LEFT,';
		}

		// TT ypos
		if( $cfg->get('com_calTTPosY') == 'ABOVE' ){
			$ttYPos = ' ABOVE,';
		}

		/* TT shadow in inside the positions
		* shadowX is fixec with 15px (above)
		* shadowY is fixed with -10px (right)
		* we also define here the shadow color (fix value - can overridden by the config later)
		*/
		if( $cfg->get('com_calTTShadow') == '1' ){
			$ttShadow 		= ' SHADOW,';
			$ttShadowColor 	= ' SHADOWCOLOR, \'#999999\',';

			if( $cfg->get('com_calTTShadowX') == '1' ){
				$ttShadowX = ' SHADOWX, -4,';
			}

			if( $cfg->get('com_calTTShadowY') == '1' ){
				$ttShadowY = ' SHADOWY, -4,';
			}
		}

		$link = $this->event->viewDetailLink($this->event->yup(),$this->event->mup(),$this->event->dup(),false);
		$link = JRoute::_($link.$this->_datamodel->getCatidsOutLink());

		$cellString .= '<hr   class="jev-click-to-open"/>'
		. '<small class="jev-click-to-open"><a href="'.$link.'"   title="'. JText::_('JEV_CLICK_TO_OPEN_EVENT', true).'" >' . JText::_('JEV_CLICK_TO_OPEN_EVENT') . '</a></small>'
		// Watch out for mambots !!
		. '</td></tr></table>';

		// harden the string for overlib
		$cellString =  '\'' . addcslashes($cellString, '\'') . '\'';

		// add more overlib parameters
		$cellString .= ', CAPTION, \'' . addcslashes($publish_inform_title, '\'') . '\',' . $ttYPos . $ttXPos
		. ' FGCOLOR, \'#FFFFE2\',' . $ttBGround. $ttFGround
		. $ttShadow . $ttShadowY . $ttShadowX . $ttShadowColor . ' AUTOSTATUSCAP';

		$cellString = ' onmouseover="return overlib('.htmlspecialchars($cellString).')"';
		$cellString .=' onmouseout="return nd();"';
		return $cellString;
	}

	function calendarCell_tooltip($cellDate){
		$cfg = JEVConfig::getInstance();

		$publish_inform_title 	= htmlspecialchars( $this->title );
		$publish_inform_overlay	= '';
		$cellString="";
		// The one overlay popup window defined for multi-day events.  Any number of different overlay windows
		// can be defined here and used according to the event's repeat type, length, whatever.  Note that the
		$tmp_time_info = '';
		if( $publish_inform_title ){
			if( $this->stop_publish == $this->start_publish ){
				if ($this->event->noendtime()){
					$tmp_time_info = '<br />' . $this->start_time;
				}
				else if ($this->event->alldayevent()){
					$tmp_time_info = "";
				}
				else if($this->start_time != $this->stop_time ){
					$tmp_time_info = '<br />' . $this->start_time . ' - ' . $this->stop_time_midnightFix;
				}
				else {
					$tmp_time_info = '<br />' . $this->start_time;
				}

				$publish_inform_overlay = $this->start_date	. $tmp_time_info
				;
			} else {
				if ($this->event->noendtime()){
					$tmp_time_info = '<br /><strong>' . JText::_('JEV_TIME') . ':&nbsp;</strong>' . $this->start_time;
				}
				else if($this->start_time != $this->stop_time && !$this->event->alldayevent()){
					$tmp_time_info = '<br /><strong>' . JText::_('JEV_TIME') . ':&nbsp;</strong>' . $this->start_time . '&nbsp;-&nbsp;' . $this->stop_time_midnightFix;
				}
				$publish_inform_overlay =  '<strong>' . JText::_('JEV_FROM') . ':&nbsp;</strong>' . $this->start_date . '&nbsp;'
				. '<br /><strong>' . JText::_('JEV_TO') . ':&nbsp;</strong>' . $this->stop_date
				. $tmp_time_info
				;
			}
		}

		// Event Repeat Type Qualifier and Day Within Event Quailfiers:

		if ($this->event->alldayevent() && $this->start_date==$this->stop_date){
			// just print the title
			$cellString = $publish_inform_overlay
			. '<br /><span class="fwb">' . ($this->event->isRepeat()?JText::_("JEV_REPEATING_EVENT"):JText::_('JEV_FIRST_SINGLE_DAY_EVENT') ). '</span>';
		}
		else if(( $cellDate == $this->stop_publish ) && ( $this->stop_publish == $this->start_publish )) {
			// single day event
			// just print the title
			$cellString = $publish_inform_overlay
			. '<br /><span class="fwb">' . ($this->event->isRepeat()?JText::_("JEV_REPEATING_EVENT"):JText::_('JEV_FIRST_SINGLE_DAY_EVENT') ) . '</span>';
		}elseif( $cellDate == $this->start_publish ){
			// first day of a multi-day event
			// just print the title
			$cellString = $publish_inform_overlay
			. '<br /><span class="fwb">' . JText::_('JEV_FIRST_DAY_OF_MULTIEVENT') . '</span>';
		}elseif( $cellDate == $this->stop_publish ){
			// last day of a multi-day event
			// enable an overlib popup
			$cellString = $publish_inform_overlay
			. '<br /><span class="fwb">' . JText::_('JEV_LAST_DAY_OF_MULTIEVENT') . '</span>';
		}elseif(( $cellDate < $this->stop_publish ) && ( $cellDate > $this->start_publish ) ) {
			// middle day of a multi-day event
			// enable the display of an overlib popup describing publish date
			$cellString = $publish_inform_overlay
			. '<br /><span class="fwb">' . JText::_('JEV_MULTIDAY_EVENT') . '</span>';
		}else{
			// this should never happen, but is here just in case...
			$cellString =  $publish_inform_overlay.'<br /><small><div class="probs_check_ev">Problems - check event!</div></small>';
			$title_event_link = "<div class='probs_check_ev'>Problems - check event!</div>";
		}


		ob_start();
		$templated = $this->loadedFromTemplate('month.calendar_tip', $this->event, 0);
		$res = ob_get_clean();
		if ($templated){
			$res = str_replace("[[TTTIME]]",$cellString, $res);
			return "templated".$res;
		}

		//$cellString .= '<br />'.$this->event->content();
		$link = $this->event->viewDetailLink($this->event->yup(),$this->event->mup(),$this->event->dup(),false);
		$link = JRoute::_($link.$this->_datamodel->getCatidsOutLink());

		$cellString .= '<hr   class="jev-click-to-open"/>'
		. '<small   class="jev-click-to-open"><a href="'.$link.'" title="'. JText::_('JEV_CLICK_TO_OPEN_EVENT', true).'" >' . JText::_('JEV_CLICK_TO_OPEN_EVENT') . '</a></small>';
		return $cellString;

		// harden the string for the tooltip
		$cellString =  '\'' . addcslashes($cellString, '\'') . '\'';

	}

	function calendarCell(&$currentDay,$year,$month,$i, $slot=""){

		$cfg = JEVConfig::getInstance();

		// define start and end
		$cellStart	= '<div';
		$cellClass	= 'p0 ';
		$cellEnd		= '</div>' . "\n";

		// add the event color as the column background color
		$cellStyle = ' background-color:' . $this->event->bgcolor() . ';color:'.$this->event->fgcolor() . ';' ;

		// MSIE ignores "inherit" color for links - stupid Microsoft!!!
		$linkStyle = 'style="color:'.$this->event->fgcolor() . ';"';

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
					$title_event_link = '<a class="cal_titlelink" href="' . $link . '" '.$linkStyle.'>'
					. ( $cfg->get('com_calDisplayStarttime') ? $tmp_start_time : '' ) . ' ' . $tmpTitle . '</a>' . "\n";
				}
				$cellClass .= 'w100';
			}
		}else{
			$eventIMG	= '<img align="left" class="b1sw" src="' . JURI::root()
			. 'components/'.JEV_COM_COMPONENT.'/images/event.png" class="h12px w8px" alt=""' . ' />';

			$title_event_link = '<a class="cal_titlelink" href="' . $link . '">' . $eventIMG . '</a>' . "\n";
			$cellClass .= ' fleft w10px';
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
                                        $dom->loadHTML('<html><head><meta content="text/html; charset=utf-8" http-equiv="Content-Type"></head><body>' . htmlspecialchars($cellString) . '</body>');

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
					// TT background
					if( $cfg->get('com_calTTBackground',1) == '1' ){
						$bground =  $this->event->bgcolor();
						$fground =  $this->event->fgcolor();
					}
					else {
						$bground =  "#000000";
						$fground =   "#ffffff";
					}
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

				$html =  $cellStart . ' class="' . $cellClass . '" style="'.$cellStyle.'">' . $this->tooltip( $title , $cellString, $title_event_link) . $cellEnd;

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
		return $cellStart . ' class="' . $cellClass . '" style="'.$cellStyle.'" ' . $cellString . ">\n" . $title_event_link . $cellEnd;
	}

	function tooltip($tooltiptitle, $tooltipcontent, $link = false)
	{
		// Backwards compatible version with only 2 arguments called
		if (!$link){
			$tooltip	= htmlspecialchars($tooltiptitle, ENT_QUOTES);
			$tip = '<span class="editlinktip hasjevtip" title="'.$tooltip.'" rel=" ">'.$tooltipcontent.'</span>';
			return $tip;
		}
		$tooltiptitle	= htmlspecialchars($tooltiptitle,ENT_QUOTES);
		$tooltipcontent	= htmlspecialchars($tooltipcontent,ENT_QUOTES);

		$tip = '<span class="editlinktip hasjevtip" title="'.$tooltiptitle.'" data-content="'.$tooltipcontent.'" >'.$link.'</span>';

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
	
	protected function correctTooltipLanguage($tip){
		return str_replace(JText::_("JEV_FIRST_DAY_OF_MULTIEVENT"), JText::_("JEV_MULTIDAY_EVENT"), $tip);
	}
	
}
