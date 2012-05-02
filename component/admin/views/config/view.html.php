<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: view.html.php 2256 2011-06-29 08:29:20Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * HTML View class for the component
 *
 * @static
 */
class AdminConfigViewconfig extends JEventsAbstractView 
{


	function edit($tpl = null)
	{
		//JRequest::setVar( 'hidemainmenu', 1 );
		
		// WHY THE HELL DO THEY BREAK PUBLIC FUNCTIONS !!!
		if (JVersion::isCompatible("1.6.0")) JHTML::stylesheet( 'administrator/components/'.JEV_COM_COMPONENT.'/assets/css/eventsadmin.css');
		else JHTML::stylesheet( 'eventsadmin.css', 'administrator/components/'.JEV_COM_COMPONENT.'/assets/css/' );

		$document =& JFactory::getDocument();
		$document->setTitle(JText::_( 'COM_JEVENTS_CONFIGURATION' ));
		
		// Set toolbar items for the page
		JToolBarHelper::title( JText::_( 'COM_JEVENTS_CONFIGURATION' ), 'jevents' );
	
		JToolBarHelper::save('config.save');
		JToolBarHelper::cancel('cpanel.cpanel');
		//JToolBarHelper::help( 'screen.config.edit', true);

		JSubMenuHelper::addEntry(JText::_( 'CONTROL_PANEL' ), 'index.php?option='.JEV_COM_COMPONENT, true);
				
		$this->dataModel = new JEventsDataModel("JEventsAdminDBModel");
		
	}	

	function dbsetup($tpl = null)
	{
		//JRequest::setVar( 'hidemainmenu', 1 );
		
		// WHY THE HELL DO THEY BREAK PUBLIC FUNCTIONS !!!
		if (JVersion::isCompatible("1.6.0")) JHTML::stylesheet( 'administrator/components/'.JEV_COM_COMPONENT.'/assets/css/eventsadmin.css');
		else JHTML::stylesheet( 'eventsadmin.css', 'administrator/components/'.JEV_COM_COMPONENT.'/assets/css/' );

		$document =& JFactory::getDocument();
		$document->setTitle(JText::_( 'DB_SETUP' ));
		
		// Set toolbar items for the page
		JToolBarHelper::title( JText::_( 'DB_SETUP' ), 'jevents' );
	
		JToolBarHelper::cancel('cpanel.cpanel');

		JSubMenuHelper::addEntry(JText::_( 'CONTROL_PANEL' ), 'index.php?option='.JEV_COM_COMPONENT, true);
						
	}	
	
	function convert($tpl = null)
	{
		
		// WHY THE HELL DO THEY BREAK PUBLIC FUNCTIONS !!!
		if (JVersion::isCompatible("1.6.0")) JHTML::stylesheet( 'administrator/components/'.JEV_COM_COMPONENT.'/assets/css/eventsadmin.css');
		else JHTML::stylesheet( 'eventsadmin.css', 'administrator/components/'.JEV_COM_COMPONENT.'/assets/css/' );

		$document =& JFactory::getDocument();
		$document->setTitle(JText::_( 'EVENTS_MIGRATION' ));
		
		// Set toolbar items for the page
		JToolBarHelper::title( JText::_( 'EVENTS_MIGRATION' ), 'jevents' );
	
		JToolBarHelper::cancel('cpanel.cpanel');

		JSubMenuHelper::addEntry(JText::_( 'CONTROL_PANEL' ), 'index.php?option='.JEV_COM_COMPONENT, true);
						
	}	

	function _defaultConfig() { ?>
	<script type="text/javascript">
	/* <![CDATA[ */

	function defaultConfig_com() {
		document.adminForm.conf_starday[0].checked = true;
		document.adminForm.conf_cache[0].checked = true;
		document.adminForm.conf_mailview[1].checked = true;
		document.adminForm.conf_print_icon_view[1] = true;
		document.adminForm.conf_byview[1].checked = true;
		document.adminForm.conf_hitsview[1].checked = 1;
		document.adminForm.conf_repeatview[1].checked = true;
		document.adminForm.conf_showrepeats[1].checked = true;
		document.adminForm.conf_hideshowbycats[0].checked = true;
		document.adminForm.conf_dateformat.value = 1;
		document.adminForm.conf_legacy_tab_extra_view.checked = true;
		document.adminForm.conf_legacy_tab_help_view.checked = true;
		document.adminForm.conf_legacy_tab_about_view.checked = true;
		document.adminForm.conf_show_editor_buttons[1].checked = true;
		document.adminForm.conf_editor_button_exceptions.value = "";
		document.adminForm.conf_single_pane_edit[0].checked = true;
		document.adminForm.conf_copyright[1].checked = true;
		document.adminForm.conf_calHeadline.value = "comp";
		document.adminForm.conf_calUseIconic[1].checked = true;
		document.adminForm.conf_navbarcolor.value = "green";
		document.adminForm.conf_defColor[2].checked = true;
		document.adminForm.conf_calSimpleEventForm[0].checked = true;
		document.adminForm.conf_calForceCatColorEventForm.value = 0;
		document.adminForm.conf_calEventListRowsPpg.value = 15;
		document.adminForm.conf_calUseStdTime[1].checked = true;
		document.adminForm.conf_calCutTitle.value = "20";
		document.adminForm.conf_calMaxDisplay.value = "5";
		document.adminForm.conf_calDisplayStarttime[1].checked = true;
		document.adminForm.conf_calViewName.value = "default";
	}

	function defaultConfig_rss() {
		document.adminForm.conf_rss_cache[0].checked = true;
		document.adminForm.conf_rss_cache_time.value = 3600;
		document.adminForm.conf_rss_count.value = 5;
		document.adminForm.conf_rss_live_bookmarks[0].checked = true;
		document.adminForm.conf_rss_modid.value = 0;
		document.adminForm.conf_rss_title.value = "Powered by JEvents!";
		document.adminForm.conf_rss_description.value = "JEvents Syndication for Joomla";
		document.adminForm.conf_rss_limit_text[0].checked = true;
		document.adminForm.conf_rss_text_length.value = 20;
	}
	
	function defaultConfig_cal() {
		document.adminForm.conf_modCalDispLastMonth.value =  "NO";
		document.adminForm.conf_modCalDispLastMonthDays.value = "0";
		document.adminForm.conf_modCalDispNextMonth.value =  "NO";
		document.adminForm.conf_modCalDispNextMonthDays.value = "0";
		document.adminForm.conf_modCalLinkCloaking[0].checked = true;

	}
	function defaultConfig_latest() {
		document.adminForm.conf_modLatestMaxEvents.value = 5;
		document.adminForm.conf_modLatestMode.value = 0;
		document.adminForm.conf_modLatestDays.value = 20;
		document.adminForm.conf_modLatestNoRepeat[0].checked =  true;
		document.adminForm.conf_modLatestDispLinks[1].checked =  true;
		document.adminForm.conf_modLatestDispYear[0].checked =  true;
		document.adminForm.conf_modLatestCustFmtStr.value = "${eventDate}[!a: - ${endDate(%I:%M%p)}]\n${title}";
		document.adminForm.conf_modLatestDisDateStyle[0].checked =  true;
		document.adminForm.conf_modLatestDisTitleStyle[0].checked = true;
		document.adminForm.conf_modLatestLinkToCal[0].checked = true;
		document.adminForm.conf_modLatestLinkCloaking[0].checked = true;
		document.adminForm.conf_modLatestSortReverse[0].checked = true;
	}
	function defaultConfig_tooltip() {
		document.adminForm.conf_calTTBackground[1].checked = true;
		document.adminForm.conf_calTTPosX[2].checked = true;
		document.adminForm.conf_calTTPosY[1].checked = true;
		document.adminForm.conf_calTTShadow[1].checked = true;
		document.adminForm.conf_calTTShadowX[0].checked = true;
		document.adminForm.conf_calTTShadowY[0].checked = true;
	}

	function defaultConfig_all() {
		defaultConfig_com();
		defaultConfig_rss();
		defaultConfig_cal();
		defaultConfig_latest();
		defaultConfig_tooltip();
	}
	/* ]]> */
	</script>
	<?php
	}


	
	/**
	* Creates a help icon with link to help information as onclick event
	*
	* if $help is url, link opens a new window with target url
	* if $help is text, text is shown in a sticky overlib window with close button
	*
	* @static
	* @param	$help		string	help text (html text or url to target)
	* @param	$caption	string	caption of overlib window
	* @return				string	html sting
	*/
	function help ( $help='help text', $caption='') {

		

		$compath = JURI::root() . 'administrator/components/'.JEV_COM_COMPONENT;
		$imgpath = $compath . '/assets/images';

		if (empty($caption)) $caption = '&nbsp;';

		if (substr($help, 0, 7) == 'http://' || substr($help, 0, 8) == 'https://') {
			//help text is url, open new window
			$onclick_cmd = "window.open(\"$help\", \"help\", \"height=700,width=800,resizable=yes,scrollbars\");return false";
		} else {
			// help text is plain text with html tags
			// prepare text as overlib parameter
			// escape ", replace new line by space
			$help = htmlspecialchars($help, ENT_QUOTES);
			$help = str_replace('&quot;', '\&quot;', $help);
			$help = str_replace("\n", " ", $help);

			$ol_cmds = 'RIGHT, ABOVE, VAUTO, WRAP, STICKY, CLOSECLICK, CLOSECOLOR, "white"';
			$ol_cmds .= ', CLOSETEXT, "<span style=\"border:solid white 1px;padding:0px;margin:1px;\"><b>X</b></span>"';
			$onclick_cmd = 'return overlib("'.$help.'", ' . $ol_cmds . ', CAPTION, "'.$caption.'")';
		}

		// RSH 10/11/10 - Added float:none for 1.6 compatiblity - The default template was floating images to the left
		$str = '<img border="0" style="float: none; vertical-align:bottom; cursor:help;" alt="'. JText::_('JEV_HELP') . '"'
		. ' title="' . JText::_('JEV_HELP') .'"'
		. ' src="' . $imgpath . '/help_ques_inact.gif"'
		. ' onmouseover=\'this.src="' . $imgpath . '/help_ques.gif"\''
		. ' onmouseout=\'this.src="' . $imgpath . '/help_ques_inact.gif"\''
		. ' onclick=\'' . $onclick_cmd . '\'>';

		return $str;
	}

}