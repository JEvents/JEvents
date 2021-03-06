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

use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarButton;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\String\StringHelper;

jimport('joomla.html.toolbar.button');
jimport('cms.toolbar.button');

class JButtonJev extends ToolbarButton
{
	/**
	 * Button type
	 *
	 * @access    protected
	 * @var        string
	 */
	var $_name = 'Jev';

	function fetchButton($type = 'Jev', $icon = '', $text = '', $task = '', $list = '')
	{

		$i18n_text = Text::_($text);
		$class     = $this->fetchIconClass($icon);
		$doTask    = $this->_getCommand($text, $task, $list);

		$html = "<a href=\"#\" onclick=\"$doTask;return false;\" class=\"toolbar\">\n";
		$html .= "<span class=\"$class\" title=\"$i18n_text\">\n";
		$html .= "</span>\n";
		$html .= "$i18n_text\n";
		$html .= "</a>\n";

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

class JButtonJevlink extends ToolbarButton
{
	/**
	 * Button type
	 *
	 * @access    protected
	 * @var        string
	 */
	var $_name = 'Jevlink';


	function fetchButton($type = 'Jevlink', $icon = '', $text = '', $task = '', $list = '')
	{

		$i18n_text = Text::_($text);
		$class     = $this->fetchIconClass($icon);
		$doTask    = $this->_getCommand($text, $task, $list);

		$html = "<a href=\"$doTask\"  class=\"toolbar\">\n";
		$html .= "<span class=\"$class\" title=\"$i18n_text\">\n";
		$html .= "</span>\n";
		$html .= "$i18n_text\n";
		$html .= "</a>\n";

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

class JButtonJevconfirm extends JtoolbarButton
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

		$html = "<a href=\"#\" onclick=\"$doTask;return false;\" class=\"toolbar\">\n";
		$html .= "<span class=\"$class\" title=\"$text\">\n";
		$html .= "</span>\n";
		$html .= "$text\n";
		$html .= "</a>\n";

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
