<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: jevbuttons.php 2749 2011-10-13 08:54:34Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */


// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
jimport('cms.toolbar.button');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarButton;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\String\StringHelper;
use Joomla\CMS\Component\ComponentHelper;


class ToolbarButtonJev extends ToolbarButton
{
	/**
	 * Button type
	 *
	 * @access    protected
	 * @var        string
	 */
	var $_name = 'Jev';

	function fetchButton($type = 'Jev', $name = '', $text = '', $task = '', $list = true, $hideMenu = false)
	{

		$i18n_text = Text::_($text);
		$class     = $this->fetchIconClass($name);
		$doTask    = $this->_getCommand($text, $task, $list);

		if ($name == "apply" || $name == "new" || $name == "save")
		{
			$btnClass  = "btn btn-small btn-success";
			$iconWhite = "icon-white";
		}
		else
		{
			$btnClass  = "btn btn-small";
			$iconWhite = "";
		}
		$btnClass = ComponentHelper::getParams(JEV_COM_COMPONENT)->get("useboostrap", 1) ? $btnClass : "";

		$html = "<button href=\"#\" onclick=\"$doTask\" class=\"" . $btnClass . "\">\n";
		$html .= "<i class=\"$class $iconWhite\">\n";
		$html .= "</i>\n";
		$html .= "$i18n_text\n";
		$html .= "</button>\n";

		return $html;
	}

	/**
	 * Get the JavaScript command for the button
	 *
	 * @access    private
	 *
	 * @param    string  $name The task name as seen by the user
	 * @param    string  $task The task used by the application
	 * @param     ???        $list
	 * @param    boolean $hide
	 *
	 * @return    string    JavaScript command string
	 * @since     1.5
	 */
	function _getCommand($name, $task, $list)
	{

		$todo    = StringHelper::strtolower(Text::_($name));
		$message = Text::sprintf('Please make a selection from the list to', $todo);
		$message = addslashes($message);

		$submitbutton = "Joomla.submitbutton";
		if ($list)
		{
			$cmd = "if(document.adminForm.boxchecked.value==0){alert('$message');}else{  $submitbutton('$task')};return false;";
		}
		else
		{
			$cmd = "$submitbutton('$task');return false;";
		}


		return $cmd;
	}

	/**
	 * Get the button CSS Id
	 *
	 * @access    public
	 * @return    string    Button CSS Id
	 * @since     1.5
	 */
	function fetchId($type = 'Js', $icon = '', $text = '', $task = '', $listSelect = '', $js = '')
	{

		return $this->_parent->getName() . '-' . $icon;
	}
}

class_alias("ToolbarButtonJev", "JToolbarButtonJev");

class ToolbarButtonJevlink extends ToolbarButton
{
	/**
	 * Button type
	 *
	 * @access    protected
	 * @var        string
	 */
	var $_name = 'Jevlink';


	function fetchButton($type = 'Jevlink', $name = '', $text = '', $task = '', $list = '')
	{

		$i18n_text = Text::_($text);
		$class     = $this->fetchIconClass($name);
		$doTask    = $this->_getCommand($text, $task, $list);

		if ($name == "cancel")
		{
			$btnClass  = "btn btn-small btn-danger";
			$iconWhite = "icon-white";
		}
		else
		{
			$btnClass  = "btn btn-small";
			$iconWhite = "";
		}
		$btnClass = ComponentHelper::getParams(JEV_COM_COMPONENT)->get("useboostrap", 1) ? $btnClass : "";

		$html = "<button href=\"#\" onclick=\"$doTask\" class=\"" . $btnClass . "\">\n";
		$html .= "<i class=\"$class\" title=\"$i18n_text\">\n";
		$html .= "</i>\n";
		$html .= "$i18n_text\n";
		$html .= "</button>\n";

		return $html;
	}

	/**
	 * Get the JavaScript command for the button
	 *
	 * @access    private
	 *
	 * @param    string  $name The task name as seen by the user
	 * @param    string  $task The task used by the application
	 * @param     ???        $list
	 * @param    boolean $hide
	 *
	 * @return    string    JavaScript command string
	 * @since     1.5
	 */
	function _getCommand($name, $task, $list)
	{

		$Itemid = Factory::getApplication()->input->getInt("Itemid");
		$link   = Route::_("index.php?option=" . JEV_COM_COMPONENT . "&task=$task&Itemid=$Itemid");

		return $link;
	}

	/**
	 * Get the button CSS Id
	 *
	 * @access    public
	 * @return    string    Button CSS Id
	 * @since     1.5
	 */
	function fetchId($type = 'Js', $icon = '', $text = '', $task = '', $listSelect = '', $js = '')
	{

		return $this->_parent->getName() . '-' . $icon;
	}
}

class_alias("ToolbarButtonJevlink", "JToolbarButtonJevlink");

class ToolbarButtonJevconfirm extends ToolbarButton
{
	/**
	 * Button type
	 *
	 * @access    protected
	 * @var        string
	 */
	var $_name = 'JevConfirm';

	function fetchButton($type = 'Confirm', $msg = '', $name = '', $text = '', $task = '', $list = true, $hideMenu = false, $jstestvar = false)
	{

		$text   = Text::_($text);
		$msg    = Text::_($msg, true);
		$class  = $this->fetchIconClass($name);
		$doTask = $this->_getCommand($msg, $name, $task, $list, $hideMenu, $jstestvar);

		$btnClass = ComponentHelper::getParams(JEV_COM_COMPONENT)->get("useboostrap", 1) ? "btn btn-small" : "";

		$html = "<button href=\"#\" onclick=\"$doTask\" class=\"$btnClass\">\n";
		$html .= "<span class=\"$class\">\n";
		$html .= "</span>\n";
		$html .= "$text\n";
		$html .= "</button>\n";

		return $html;
	}

	/**
	 * Get the JavaScript command for the button
	 *
	 * @access    private
	 *
	 * @param    object $definition Button definition
	 *
	 * @return    string    JavaScript command string
	 * @since     1.5
	 */
	function _getCommand($msg, $name, $task, $list, $hide, $jstestvar = false)
	{

		$todo         = StringHelper::strtolower(Text::_($name));
		$message      = Text::sprintf('Please make a selection from the list to %s', $todo);
		$message      = addslashes($message);
		$submitbutton = "Joomla.submitbutton";

		if ($hide)
		{
			if ($list)
			{
				$cmd = "if(document.adminForm.boxchecked.value==0){
					alert('$message');
				}
				else{
					
					if($jstestvar==1) {
						if (confirm('$msg')){
							$submitbutton('$task');
						}
						return false;
					}
					$submitbutton('$task');
				}";
			}
			else
			{
				$cmd = "if($jstestvar==1) {
						if (confirm('$msg')){
							$submitbutton('$task');
						}
						return false;
					}
					$submitbutton('$task');
				";
			}
		}
		else
		{
			if ($list)
			{
				$cmd = "if(document.adminForm.boxchecked.value==0){
					alert('$message');
				}
				else{
					if($jstestvar==1) {
						if (confirm('$msg')){
							$submitbutton('$task');
						}
						return false;
					}
					$submitbutton('$task');
				}";
			}
			else
			{
				$cmd = "
				if($jstestvar==1) {
					if (confirm('$msg')){
						$submitbutton('$task');
					}
					return false;
				}
				$submitbutton('$task');
				";
			}
		}

		return $cmd;
	}

	/**
	 * Get the button CSS Id
	 *
	 * @access    public
	 * @return    string    Button CSS Id
	 * @since     1.5
	 */
	function fetchId($type = 'Confirm', $msg = '', $name = '', $text = '', $task = '', $list = true, $hideMenu = false, $jstestvar = false)
	{

		return $this->_parent->getName() . '-' . $name;
	}
}

class_alias("ToolbarButtonJevconfirm", "JToolbarButtonJevconfirm");

class ToolbarButtonJevconfirmvar extends ToolbarButtonJevconfirm
{
	/**
	 * Button type
	 *
	 * @access    protected
	 * @var        string
	 */
	var $_name = 'Jevconfirm';

	/**
	 * Fetch the HTML for the button
	 *
	 * @param   string  $type     Unused string.
	 * @param   string  $msgvar   Message to render as a javascript variable!
	 * @param   string  $name     Name to be used as apart of the id
	 * @param   string  $text     Button text
	 * @param   string  $task     The task associated with the button
	 * @param   boolean $list     True to allow use of lists
	 * @param   boolean $hideMenu True to hide the menu on click
	 *
	 * @return  string   HTML string for the button
	 *
	 * @since   3.0
	 */
	public function fetchButton($type = 'Confirmvar', $msgvar = '', $name = '', $text = '', $task = '', $list = true, $hideMenu = false, $btnClass = false, $tooltiptitle = "", $tooltipcontent = "")
	{
		// Store all data to the options array for use with JLayout
		$options           = array();
		$options['text']   = \JText::_($text);
		$options['msgvar'] = $msgvar;

		// We want to make sure carriage returns in the message are respected
		//$options['msg'] = str_replace("\\\\n" , "\n", $options['msg']);

		$options['class']  = $this->fetchIconClass($name);
		$options['gsl-icon']  = $name;
		$options['doTask'] = $this->getButtonCommand($options['msgvar'], $name, $task, $list);

		$options['btnClass'] = 'btn btn-small button-' . $name;

		// Add name as class too
		$options['btnClass'] .= " " . str_replace(array(".", "(", ")") , "", $task);

		if ($btnClass)
		{
			$options['btnClass'] .= " " . $btnClass;
		}

		$tooltip = '';
		if ($tooltiptitle && $tooltipcontent)
		{
			$tooltip = ' data-yspoptitle = "' . \JText::_($tooltiptitle, true) . '"'
				. '  data-yspopcontent = "' . \JText::_($tooltipcontent, true) . '" '
				. ' data-yspopoptions= \'{"mode" : "hover", "offset" : 20,"delayHide" : 200, "pos" : "right"}\'';

			$options['btnClass'] .= " hasYsPopover";
		}
		$options['tooltip'] = $tooltip;

		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new FileLayout('joomla.toolbar.confirmvar');

		$output =  $layout->render($options);

		return $output;

	}

	protected function getButtonCommand($msgvar, $name, $task, $list)
	{
		\JText::script('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');

		$msgvar = str_replace("\\n", "<br>", $msgvar);
		$msgvar = str_replace("\n", "<br>", $msgvar);

		if (strpos($task, ".") !== false)
		{
			//$cmd = "let msg = " . $msgvar . "; if (confirm(msg.replace(/\\\\n/g, '\\n'))) { Joomla.submitbutton('" . $task . "'); }";
			$cmd = "let msg = " . $msgvar . "; if (msg.length == 0) { Joomla.submitbutton('" . $task . "');return false; };gslUIkit.modal.confirm(msg.replace(/\\n/g, '<br>')).then(() => { Joomla.submitbutton('" . $task . "'); }, () => {try { event.stopPropagation();} catch (e) {}; return false;})";
		}
		else
		{
			// Javascript !
			//$cmd = "let msg = " . $msgvar . "; if (confirm(msg.replace(/\\\\n/g, '\\n'))) { " . $task . "; }";
			$cmd = "let msg = " . $msgvar . "; gslUIkit.modal.confirm(msg.replace(/\\n/g, '<br>')).then( () => { " . $task . "; }, () => {try { event.stopPropagation();} catch (e) {}; return false;})";
		}

		if ($list)
		{
			$alert = "alert(Joomla.JText._('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'));";
			$cmd   = "if (document.adminForm.boxchecked.value == 0) { " . $alert . " } else { " . $cmd . " }";
		}

		return $cmd;
	}

}

class_alias("ToolbarButtonJevconfirmvar", "JToolbarButtonJevconfirmvar");