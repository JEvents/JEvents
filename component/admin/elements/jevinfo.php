<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: jevinfo.php 1277 2010-10-11 22:04:39Z royceharding $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
jimport("joomla.html.parameter.element");

class JElementJevinfo extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'Jevinfo';

	function fetchTooltip($label, $description, &$node, $control_name, $name) {
		return '&nbsp;';
	}

	function fetchElement($name, $value, &$node, $control_name)
	{

		// Must load admin language files
		$lang =& JFactory::getLanguage();
		$lang->load("com_jevents", JPATH_ADMINISTRATOR);

		$help = $node->attributes('help');
		// RSH 10/5/10 Added this for J!1.6 - $help is now an JXMLElement
		if ( (!is_null($help)) && (version_compare(JVERSION, '1.6.0', ">=")) ) {
			if (is_object($help)) $help =  $help->data();
			$help = ( (isset($help)) && (strlen($help) <= 0)) ? null : $help;
		}
		if (!is_null($help)) {
			$parts = explode(",",$value);
			$helps = explode(",",$help);
			foreach ($parts as $key=>$valuepart) {
				$help = $helps[$key];	
				list($helpfile,$varname,$part) = explode("::",$help);
				JEVHelper::loadOverlib();
				$lang =& JFactory::getLanguage();
				$langtag  = $lang->getTag();
				if( file_exists( JPATH_COMPONENT_ADMINISTRATOR . '/help/' . $langtag . '/'.$helpfile )){
					$jeventHelpPopup =  JPATH_COMPONENT_ADMINISTRATOR . '/help/' . $langtag . '/'.$helpfile ;
				}
				else {
					$jeventHelpPopup =  JPATH_COMPONENT_ADMINISTRATOR . '/help/en-GB/'.$helpfile ;
				}
				include($jeventHelpPopup);
				$help = $this->help($$varname, $part);
				$parts[$key]=JText::_($valuepart).$help;
			}
			$value = implode(", ",$parts);
		}
		return "<strong style='color:#993300'>".JText::_($value)  ."</strong>";
		
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
		. ' onclick=\'' . $onclick_cmd . '\' />';

		return $str;
	}

}