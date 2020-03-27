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
			$cmd = "javascript:if(document.adminForm.boxchecked.value==0){alert('$message');}else{  $submitbutton('$task')};return false;";
		}
		else
		{
			$cmd = "javascript:$submitbutton('$task');return false;";
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
				$cmd = "javascript:if(document.adminForm.boxchecked.value==0){
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
				$cmd = "javascript:
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
		else
		{
			if ($list)
			{
				$cmd = "javascript:if(document.adminForm.boxchecked.value==0){
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
				$cmd = "javascript:
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
