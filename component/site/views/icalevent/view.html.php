<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: view.html.php 2979 2011-11-10 13:50:14Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\String\StringHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Uri\Uri;

if (File::exists(JPATH_ADMINISTRATOR . '/includes/toolbar.php')) {
    require_once(JPATH_ADMINISTRATOR . '/includes/toolbar.php');
}

/**
 * HTML View class for the component frontend
 *
 * @static
 */
include_once(JEV_ADMINPATH . "/views/icalevent/view.html.php");

class ICalEventViewIcalevent extends AdminIcaleventViewIcalevent
{

	var $jevlayout = null;

	function __construct($config = array())
	{

		if (File::exists(JPATH_ADMINISTRATOR . '/includes/toolbar.php')) {
			require_once(JPATH_ADMINISTRATOR . '/includes/toolbar.php');
		}
		parent::__construct($config);

		// used only for helper functions
		$this->jevlayout = "default";
		$this->addHelperPath(realpath(dirname(__FILE__) . "/../default/helpers"));
		$this->addHelperPath(JPATH_BASE . '/' . 'templates' . '/' . Factory::getApplication()->getTemplate() . '/' . 'html' . '/' . JEV_COM_COMPONENT . '/' . "helpers");
		// stop crawler and set meta tag.
		JEVHelper::checkRobotsMetaTag();

		// Call the MetaTag setter function.
		JEVHelper::SetMetaTags();
	}

	function edit($tpl = null)
	{
		// Ensure jQuery is loaded untill fully removed.
		HTMLHelper::_('jquery.framework');
		
		$app    = Factory::getApplication();
		$input  = $app->input;

		$document = Factory::getDocument();
		$user = Factory::getUser();

		// Set editstrings var just in case and to avoid IDE reporting not set.
		$editStrings = "";
		include(JEV_ADMINLIBS . "/editStrings.php");
		$document->addScriptDeclaration($editStrings);

		JEVHelper::script('editicalJQ.js', 'components/' . JEV_COM_COMPONENT . '/assets/js/');
		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		if (!GSLMSIE10 && $params->get("newfrontendediting", 1) == 1)
		{
			JEVHelper::script('editicalGSL.js', 'components/' . JEV_COM_COMPONENT . '/assets/js/');
		}
		else if (!GSLMSIE10 && $params->get("newfrontendediting", 1) == 2)
		{
			JEVHelper::script('editicalUIKIT.js', 'components/' . JEV_COM_COMPONENT . '/assets/js/');
		}
		JEVHelper::script('JevStdRequiredFieldsJQ.js', 'components/' . JEV_COM_COMPONENT . '/assets/js/');

		if (strlen($this->row->title()) > 0)
		{
			// Set toolbar items for the page
			JToolbarHelper::title(Text::_('EDIT_ICAL_EVENT'), 'jevents');
			$document->setTitle(Text::_('EDIT_ICAL_EVENT'));
		}
		else
		{
			// Set toolbar items for the page
			JToolbarHelper::title(Text::_('CREATE_ICAL_EVENT'), 'jevents');
			$document->setTitle(Text::_('CREATE_ICAL_EVENT'));
		}

		$bar = JToolBar::getInstance('toolbar');

		if ($this->id > 0)
		{
			if ($this->editCopy)
			{

				if (JEVHelper::isEventEditor() || JEVHelper::canEditEvent($this->row))
				{
					$this->toolbarConfirmButton("icalevent.apply", Text::_("JEV_SAVE_COPY_WARNING"), 'apply', 'apply', 'JEV_SAVE', false);
				}
				$this->toolbarConfirmButton("icalevent.save", Text::_("JEV_SAVE_COPY_WARNING"), 'save', 'save', 'JEV_SAVE_CLOSE', false);
			}
			else
			{
				if (JEVHelper::isEventEditor() || JEVHelper::canEditEvent($this->row))
				{
					$this->toolbarConfirmButton("icalevent.apply", Text::_("JEV_SAVE_ICALEVENT_WARNING"), 'apply', 'apply', 'JEV_SAVE', false);
				}
				$this->toolbarConfirmButton("icalevent.save", Text::_("JEV_SAVE_ICALEVENT_WARNING"), 'save', 'save', 'JEV_SAVE_CLOSE', false);
			}
		}
		else
		{
			$canEditOwn = false;
			$params     = ComponentHelper::getParams(JEV_COM_COMPONENT);
			if (!$params->get("authorisedonly", 0))
			{
				$juser      = Factory::getUser();
				$canEditOwn = $juser->authorise('core.edit.own', 'com_jevents');
			}
			else if (JEVHelper::canEditOwnEventNewEventOnlyCheck())
			{
				$canEditOwn = true;
			}
			if (JEVHelper::isEventEditor() || $canEditOwn)
			{
				$this->toolbarButton("icalevent.apply", 'apply', 'apply', 'JEV_SAVE', false);
			}
			$this->toolbarButton("icalevent.save", 'save', 'save', 'JEV_SAVE_CLOSE', false);
		}

		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);

		$evedrd = 'icalevent.edit_cancel';

        //Set previous page
        $session = Factory::getSession();
        $sinput   = new \Joomla\Input\Input($_SERVER);
        $referer = $sinput->getString('HTTP_REFERER', null);
        $current = Uri::getInstance();
        $current = $current->toString(array('scheme', 'host', 'port', 'path', 'query'));
        if ($referer && $referer !== $current) {
            $session->set('jev_referer', $referer, 'extref');
        }
		else {
			$session->set('jev_referer', '', 'extref');
		}
		$ref = $session->get('jev_referer', 'blank', 'extref');
		//echo "ref = $ref $referer $current<br>";

		if ($params->get("editpopup", 0))
		{
			$document->addStyleDeclaration("div#toolbar-box{margin:10px 10px 0px 10px;} div#jevents {margin:0px 10px 10px 10px;} ");
			$this->toolbarButton("icalevent.close", 'cancel', 'cancel', 'JEV_SUBMITCANCEL', false);
			$input->set('tmpl', 'component'); //force the component template
		}
		else
		{
			if ($this->id > 0)
			{
				$this->toolbarButton("icalrepeat.detail", 'cancel', 'cancel', 'JEV_SUBMITCANCEL', false);
			}
			else
			{
				$this->toolbarButton($evedrd, 'cancel', 'cancel', 'JEV_SUBMITCANCEL', false);
			}
		}

		// I pass in the rp_id so that I can return to the repeat I was viewing before editing
		$this->rp_id = $input->getInt("rp_id", 0);

		if (!$params->get("newfrontendediting", 1))
		{
			$this->_adminStart();
		}

		// load Joomla javascript classes
		HTMLHelper::_('behavior.core');
		$this->setLayout("edit");

		JEVHelper::componentStylesheet($this, "editextra.css");
		jimport('joomla.filesystem.file');

		// Lets check if we have editted before! if not... rename the custom file.
		if (File::exists(JPATH_SITE . "/components/com_jevents/assets/css/jevcustom.css"))
		{
			// It is definitely now created, lets load it!
			JEVHelper::stylesheet('jevcustom.css', 'components/' . JEV_COM_COMPONENT . '/assets/css/');
		}

        $this->setupEditForm();

		parent::displaytemplate($tpl);

		if (!$params->get("newfrontendediting", 1))
		{
    		$this->_adminEnd();
		}
	}

	function toolbarConfirmButton($task = '', $msg = '', $icon = '', $iconOver = '', $alt = '', $listSelect = true)
	{

		$bar = JToolBar::getInstance('toolbar');

		// Add a standard button
		$bar->appendButton('Jevconfirm', $msg, $icon, $alt, $task, $listSelect, false, "document.adminForm.updaterepeats.value");
	}

	function toolbarButton($task = '', $icon = '', $iconOver = '', $alt = '', $listSelect = true)
	{

		$bar = JToolBar::getInstance('toolbar');

		// Add a standard button
		$bar->appendButton('Jev', $icon, $alt, $task, $listSelect);
	}

	function _adminStart()
	{

		list($this->year, $this->month, $this->day) = JEVHelper::getYMD();
		$app             = Factory::getApplication();
		$this->Itemid    = JEVHelper::getItemid();
		$this->datamodel = new JEventsDataModel();
		$app->triggerEvent('onJEventsHeader', array($this));
		?>
		<div style="clear:both"
		<?php
		$params    = ComponentHelper::getParams(JEV_COM_COMPONENT);
		echo (!$app->isClient('administrator') && $params->get("darktemplate", 0)) ? "class='jeventsdark'" : "class='jeventslight'";
		?>>
		<div id="toolbar-box">
			<?php
			$bar     = JToolBar::getInstance('toolbar');
			$barhtml = $bar->render();
			echo $barhtml;
			?>
		</div>
		<?php
	}

	function _adminEnd()
	{

		?>
		</div>
		<?php

		Factory::getApplication()->triggerEvent('onJEventsFooter', array($this));
	}

	function toolbarLinkButton($task = '', $icon = '', $iconOver = '', $alt = '')
	{

		$bar = JToolBar::getInstance('toolbar');

		// Add a standard button
		$bar->appendButton('Jevlink', $icon, $alt, $task, false);
	}

	// This handles all methods where the view is passed as the first argument

	function __call($name, $arguments)
	{

		if (strpos($name, "_") === 0)
		{
			$name = "ViewHelper" . ucfirst(StringHelper::substr($name, 1));
		}
		$helper = ucfirst($this->jevlayout) . ucfirst($name);
		if (!$this->loadHelper($helper))
		{
			$helper = "Default" . ucfirst($name);
			if (!$this->loadHelper($helper))
			{
				return;
			}
		}
		$args = array_unshift($arguments, $this);
		if (class_exists($helper))
		{
			if (class_exists("ReflectionClass"))
			{
				$reflectionObj = new ReflectionClass($helper);
				if (method_exists($reflectionObj, "newInstanceArgs"))
				{
					$var = $reflectionObj->newInstanceArgs($arguments);
				}
				else
				{
					$var = $this->CreateClass($helper, $arguments);
				}
			}
			else
			{
				$var = $this->CreateClass($helper, $arguments);
			}

			return;
		}
		else if (is_callable($helper))
		{
			return call_user_func_array($helper, $arguments);
		}
	}

	function loadHelper($file = null)
	{

		if (function_exists($file) || class_exists($file))
			return true;

		// load the template script
		jimport('joomla.filesystem.path');
		$helper = Path::find($this->_path['helper'], $this->_createFileName('helper', array('name' => $file)));

		if ($helper != false)
		{
			// include the requested template filename in the local scope
			include_once $helper;
		}

		return $helper;
	}

	protected function CreateClass($className, $params)
	{

		switch (count($params))
		{
			case 0:
				return new $className();
				break;
			case 1:
				return new $className($params[0]);
				break;
			case 2:
				return new $className($params[0], $params[1]);
				break;
			case 3:
				return new $className($params[0], $params[1], $params[2]);
				break;
			case 4:
				return new $className($params[0], $params[1], $params[2], $params[3]);
				break;
			case 5:
				return new $className($params[0], $params[1], $params[2], $params[3], $params[4]);
				break;
			case 6:
				return new $className($params[0], $params[1], $params[2], $params[3], $params[4], $params[5]);
				break;
			case 7:
				return new $className($params[0], $params[1], $params[2], $params[3], $params[4], $params[5], $params[6]);
				break;
			case 8:
				return new $className($params[0], $params[1], $params[2], $params[3], $params[4], $params[5], $params[6], $params[7]);
				break;
			case 9:
				return new $className($params[0], $params[1], $params[2], $params[3], $params[4], $params[5], $params[6], $params[7], $params[8]);
				break;
			case 10:
				return new $className($params[0], $params[1], $params[2], $params[3], $params[4], $params[5], $params[6], $params[7], $params[8], $params[9]);
				break;
			default:
				echo "Too many arguments";

				return null;
				break;
		}
	}

}
    
