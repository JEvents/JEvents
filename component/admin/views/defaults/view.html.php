<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: view.html.php 2942 2011-11-01 16:12:51Z carcam $
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
class AdminDefaultsViewDefaults extends JEventsAbstractView
{

	/**
	 * Defaults display function
	 *
	 * @param template $tpl
	 */
	function overview($tpl = null)
	{

		$document = JFactory::getDocument();
		$document->setTitle(JText::_('JEV_LAYOUT_DEFAULTS'));

		// Set toolbar items for the page
		JToolBarHelper::title(JText::_('JEV_LAYOUT_DEFAULTS'), 'jevents');

		JEventsHelper::addSubmenu();

		JHTML::_('behavior.tooltip');

		$db = JFactory::getDBO();
		$uri =  JFactory::getURI();

		// Get data from the model
		$model = $this->getModel();
		$items = $this->get('Data');
		$total = $this->get('Total');
		$languages = $this->get('Languages');
		$catids = $this->get('Categories');

		$language = JFactory::getApplication()->getUserStateFromRequest("jevdefaults.filter_language", 'filter_language', "*");
		$this->assign('language', $language);
		$this->assign('languages', $languages);

		$catid = JFactory::getApplication()->getUserStateFromRequest("jevdefaults.filter_catid", 'filter_catid', "");
		$catids = JHtml::_('select.options', $catids, 'value', 'text', $catid);
		$this->assign('catid', $catid);
		$this->assign('catids', $catids);

		$layouttype = JFactory::getApplication()->getUserStateFromRequest("jevdefaults.filter_layout_type", 'filter_layout_type', "jevents");
		$addonoptions = array();
		$addonoptions[] = JHTML::_('select.option', '', JText::_('JEV_SELECT_LAYOUT_TYPE'));
		$addonoptions[] = JHTML::_('select.option', 'jevents', JText::_('COM_JEVENTS'));
		$addonoptions[] = JHTML::_('select.option', 'jevpeople', JText::_('COM_JEVPEOPLE'));
		$addonoptions[] = JHTML::_('select.option', 'jevlocations', JText::_('COM_JEVLOCATIONS'));

		$addonoptions = JHtml::_('select.options', $addonoptions, 'value', 'text', $layouttype);
		$this->assign('addonoptions', $addonoptions);

		$filter_published = JFactory::getApplication()->getUserStateFromRequest("jevdefaults.filter_published", 'filter_published', "");
		$this->assign('filter_published', $filter_published);

		$user = JFactory::getUser();
		$this->assignRef('user', $user);
		$this->assignRef('items', $items);

		if (JevJoomlaVersion::isCompatible("3.0"))
		{
			$this->sidebar = JHtmlSidebar::render();
		}

		parent::displaytemplate($tpl);

	}

	function edit($tpl = null)
	{

		include_once(JPATH_ADMINISTRATOR . '/' . "includes" . '/' . "toolbar.php");

		// WHY THE HELL DO THEY BREAK PUBLIC FUNCTIONS !!!
		JEVHelper::script('editdefaults.js', 'administrator/components/' . JEV_COM_COMPONENT . '/assets/js/');

		$document = JFactory::getDocument();
		$document->setTitle(JText::_('JEV_LAYOUT_DEFAULT_EDIT'));

		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		$requiredfields = $params->get("com_jeveditionrequiredfields", "");
		if (!empty($requiredfields))
		{
			$requiredfields = "'" . implode("','", $requiredfields) . "'";
		}

		// Set toolbar items for the page
		JToolBarHelper::title(JText::_('JEV_LAYOUT_DEFAULT_EDIT'), 'jevents');

		JToolBarHelper::apply("defaults.apply");
		JToolBarHelper::save("defaults.save");
		JToolBarHelper::cancel("defaults.cancel");

		JEventsHelper::addSubmenu();

		JHTML::_('behavior.tooltip');



		$db = JFactory::getDBO();
		$uri =  JFactory::getURI();

		// Get data from the model
		$model =  $this->getModel();
		$item =  $this->get('Data');

		if (strpos($item->name, "com_") === 0)
		{
			$parts = explode(".", $item->name);
			// special numbered case e.g. managed people
			if (count($parts) == 4)
			{
				$iname = str_replace(".$parts[2].", ".", $item->name);
			}
			else
			{
				$iname = $item->name;
			}
			$this->_addPath('template', JPATH_ADMINISTRATOR . "/components/" . $parts[0] . "/views/defaults/tmpl");
			if ($item->value == "" && file_exists(JPATH_ADMINISTRATOR . "/components/" . $parts[0] . "/views/defaults/tmpl/" . $iname . ".html"))
			{
				$item->value = file_get_contents(JPATH_ADMINISTRATOR . "/components/" . $parts[0] . "/views/defaults/tmpl/" . $iname . ".html");
			}
		}

		$this->assignRef('item', $item);
		$this->assignRef('requiredfields', $requiredfields);

		parent::displaytemplate($tpl);

	}

	function showToolBar()
	{
		?>
		<div id="toolbar-box" >
			<div class="t">
				<div class="t">
					<div class="t"></div>
				</div>
			</div>
			<div class="m">
				<?php
				$bar =  JToolBar::getInstance('toolbar');
				$barhtml = $bar->render();
				//$barhtml = str_replace('href="#"','href="javascript void();"',$barhtml);
				//$barhtml = str_replace('submitbutton','return submitbutton',$barhtml);
				echo $barhtml;

				if (JevJoomlaVersion::isCompatible("3.0"))
				{
					$title = JFactory::getApplication()->JComponentTitle;
				}
				else
				{
					$title = JFactory::getApplication()->get('JComponentTitle');
				}
				echo $title;
				?>
				<div class="clr"></div>
			</div>
			<div class="b">
				<div class="b">
					<div class="b"></div>
				</div>
			</div>
		</div>
		<?php

	}

	protected
			function replaceLabels(&$value)
	{
		// non greedy replacement - because of the ?
		$value = preg_replace_callback('|{{.*?:|', 'replaceLabelsCallback', $value);

	}

}

function replaceLabelsCallback($matches)
{
	if (count($matches) == 1)
	{
		return "{{" . JText::_(substr($matches[0], 2, strlen($matches[0]) - 3)) . ":";
	}
	return "";

}
