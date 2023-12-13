<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: view.html.php 2942 2011-11-01 16:12:51Z carcam $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\String\StringHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Service\Provider\Toolbar;

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

		$app    = Factory::getApplication();
		$document = Factory::getDocument();
		$document->setTitle(Text::_('JEV_LAYOUT_DEFAULTS'));

		// Set toolbar items for the page
		JToolbarHelper::title(Text::_('JEV_LAYOUT_DEFAULTS'), 'jevents');

        JToolbarHelper::publishList('defaults.publish');
        JToolbarHelper::unpublishList('defaults.unpublish');

		$db  = Factory::getDbo();
		$uri = \Joomla\CMS\Uri\Uri::getInstance();

		// Get data from the model
		$model     = $this->getModel();
		$items     = $this->get('Data');
		$total     = $this->get('Total');
		$languages = $this->get('Languages');
		$catids    = $this->get('Categories');

        $orphans   = $this->get('OrphanData');

		$language = $app->getUserStateFromRequest("jevdefaults.filter_language", 'filter_language', "*");
		$this->language     = $language;
		$this->languages    = $languages;

		$layouttype     = $app->getUserStateFromRequest("jevdefaults.filter_layout_type", 'filter_layout_type', "jevents");
		$addonoptions   = array();
		$addonoptions[] = HTMLHelper::_('select.option', '', Text::_('JEV_SELECT_LAYOUT_TYPE'));
		$addonoptions[] = HTMLHelper::_('select.option', 'jevents', Text::_('COM_JEVENTS'));
		$addonoptions[] = HTMLHelper::_('select.option', 'jevpeople', Text::_('COM_JEVPEOPLE'));
		$addonoptions[] = HTMLHelper::_('select.option', 'jevlocations', Text::_('COM_JEVLOCATIONS'));

		$addonoptions = HTMLHelper::_('select.options', $addonoptions, 'value', 'text', $layouttype);
		$this->addonoptions = $addonoptions;

		if ($layouttype == "jevents")
		{
			$catid  = $app->getUserStateFromRequest("jevdefaults.filter_catid", 'filter_catid', "");
			$catids = HTMLHelper::_('select.options', $catids, 'value', 'text', $catid);
		}
		else
		{
			$catid  = 0;
			$catids = "";
		}
		$this->catid    = $catid;
		$this->catids   = $catids;

		$filter_published = $app->getUserStateFromRequest("jevdefaults.filter_published", 'filter_published', "");
		$this->filter_published = $filter_published;

		$user = Factory::getUser();
		$this->user     = $user;
		$this->items    = $items;
		$this->orphans  = $orphans;

	}

	function edit($tpl = null)
	{

		// WHY THE HELL DO THEY BREAK PUBLIC FUNCTIONS !!!
		JEVHelper::script('editdefaults.js', 'administrator/components/' . JEV_COM_COMPONENT . '/assets/js/');

		$document = Factory::getDocument();
		$document->setTitle(Text::_('JEV_LAYOUT_DEFAULT_EDIT'));

		$params         = ComponentHelper::getParams(JEV_COM_COMPONENT);
		$requiredfields = $params->get("com_jeveditionrequiredfields", "");
		if (!empty($requiredfields))
		{
			$requiredfields = "'" . implode("','", $requiredfields) . "'";
		}

		// Set toolbar items for the page
		JToolbarHelper::title(Text::_('JEV_LAYOUT_DEFAULT_EDIT'), 'jevents');

		JToolbarHelper::apply("defaults.apply");
		JToolbarHelper::save("defaults.save");
		JToolbarHelper::cancel("defaults.cancel");

		// Get data from the model
		$item  = $this->get('Data');

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

		$this->item             = $item;
		$this->requiredfields   = $requiredfields;

		//parent::displaytemplate($tpl);

	}

	function showToolBar()
	{

		?>
		<div id="toolbar-box">
			<div class="t">
				<div class="t">
					<div class="t"></div>
				</div>
			</div>
			<div class="m">
				<?php
				$bar     = JToolBar::getInstance('toolbar');
				$barhtml = $bar->render();
				//$barhtml = str_replace('href="#"','href="javascript void();"',$barhtml);
				//$barhtml = str_replace('submitbutton','return submitbutton',$barhtml);
				echo $barhtml;

				$title = Factory::getApplication()->JComponentTitle;

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

	protected function translationLinks($row)
	{

		if ($this->languages)
		{
			// Any existing translations ?
			$db = Factory::getDbo();
			$db->setQuery("SELECT id, language, value, state FROM #__jev_defaults where catid=" . $row->catid . " and title=" . $db->quote($row->title));
			$translations = $db->loadObjectList("language");

			?>
			<ul class="item-associations">
				<?php foreach ($this->languages as $id => $item) :

					$text = strtoupper($item->sef);
					$hasTranslation = false;
					$translationid = 0;
					if (isset($translations[$id]))
					{
						$translationid = $translations[$id]->id;
						if ($translations[$id]->value != "" && $translations[$id]->state)
						{
							$hasTranslation = true;
						}
					}
					else
					{

					}
					$url          = Route::_('index.php?option=com_jevents&task=defaults.edit&id=' . $translationid, false);
					$img          = HTMLHelper::_('image', 'mod_languages/' . $item->image . '.gif',
						$item->title,
						array('title' => $item->title),
						true
					);
					$url          = $url;// ."', '". Text::sprintf("JEV_TRANSLATE_EVENT_TO" ,  addslashes($item->title),  array('jsSafe'=>true) ) . "'); ";
					$tooltipParts = array($img, addslashes($item->title));
					$item->link   = HTMLHelper::_('tooltip', implode(' ', $tooltipParts), null, null, $text, $url, null, 'hasTooltip label label-association label-' . $item->sef . ($hasTranslation ? " hastranslation" : ""));
					?>

					<li>
						<span
								class="editlinktip hasYsPopover <?php echo ' label label-association label-' . $item->sef . ($hasTranslation ? " hastransflation" : "");?>"
						      data-yspoptitle="<?php echo Text::_('JEV_TRANSLATE_LAYOUT', array('jsSafe'=>true)); ?>"
						      data-yspopcontent="<?php echo Text::sprintf('JEV_TRANSLATE_LAYOUT_INTO', addslashes($item->title) . " " . htmlspecialchars($img), array('jsSafe'=>true)); ?>"
						>
							<a href="<?php echo $url;?>" class="gsl-button gsl-button-small <?php echo ($hasTranslation ? " gsl-button-success " : " gsl-button-primary")?> gsl-text-decoration-none">
								<?php echo $text;?>
							</a>
						</span>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php
		}
	}

}

function replaceLabelsCallback($matches)
{

	if (count($matches) == 1)
	{
		return "{{" . Text::_(StringHelper::substr($matches[0], 2, StringHelper::strlen($matches[0]) - 3)) . ":";
	}

	return "";

}
