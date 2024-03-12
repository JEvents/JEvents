<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: abstract.php 3229 2012-01-30 12:06:34Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\String\StringHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\Registry\Registry;
use Joomla\CMS\Filesystem\File;

jimport('joomla.application.component.view');

#[\AllowDynamicProperties]
class JEventsAbstractView extends Joomla\CMS\MVC\View\HtmlView
{

	function __construct($config = null)
	{

		parent::__construct($config);
		jimport('joomla.filesystem.file');

		$app = Factory::getApplication();

		if ($app->isClient('administrator'))
		{
			JEVHelper::stylesheet('jev_cp.css', 'administrator/components/' . JEV_COM_COMPONENT . '/assets/css/');
		}
		JEVHelper::stylesheet('eventsadmin.css', 'components/' . JEV_COM_COMPONENT . '/assets/css/');

		$this->_addPath('template', $this->_basePath . '/' . 'views' . '/' . 'abstract' . '/' . 'tmpl');
		// note that the name config variable is ignored in the parent construct!

		// Ok getTemplate doesn't seem to get the active menu item's template, so lets do it ourselves if it exists

		// Get current template style ID
		$page_template_id = $app->isClient('administrator') ? "0" : @$app->getMenu()->getActive()->template_style_id;

		// Check it's a valid style with simple check
		if (!($page_template_id == "" || $page_template_id == "0"))
		{
			// Load the valid style:
			$db    = Factory::getDbo();
			$query = $db->getQuery(true)
				->select('template')
				->from('#__template_styles')
				->where('id =' . $db->quote($page_template_id) . '');
			$db->setQuery($query);
			$template = $db->loadResult();

		}
		else
		{
			$template = Factory::getApplication()->getTemplate();
		}

		$theme = JEV_CommonFunctions::getJEventsViewName();
		$name  = $this->getName();
		$name  = str_replace($theme . "/", "", $name);
		$this->addTemplatePath(JPATH_BASE . '/' . 'templates' . '/' . $template . '/' . 'html' . '/' . JEV_COM_COMPONENT . '/' . $theme . '/' . $name);

		// or could have used
		//$this->addTemplatePath( JPATH_BASE.'/'.'templates'.'/'.Factory::getApplication()->getTemplate().'/'.'html'.'/'.JEV_COM_COMPONENT.'/'.$config['name'] );


	}

	/**
	 * Control Panel display function
	 *
	 * @param template $tpl
	 */
	function display($tpl = null)
	{

		$layout = $this->getLayout();

		if (method_exists($this, $layout))
		{
			$this->$layout($tpl);
			if (isset($this->jevviewdone) && $this->jevviewdone)
			{
				return;
			}
		}

		// Allow the layout to be overriden by menu parameter - this only works if its valid for the task
		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		$this->componentParams = $params;

		// layout may get re-assigned by $this->$layout($tpl); for handle different versions of Joomla
		$layout    = $this->getLayout();
		$newlayout = $params->get("overridelayout", $layout);

		// check the template layout is valid for this task
		jimport('joomla.filesystem.path');
		$filetofind = $this->_createFileName('template', array('name' => $newlayout));
		if (Path::find($this->_path['template'], $filetofind))
		{
			$this->setLayout($newlayout);
		}

		// Do we need to fall back to old MSIE layouts ??
		// Do not do this in Internet Explorer 10 or lower (Note that MSIE 11 changed the app name to Trident)
		if (defined("GSLMSIE10") && GSLMSIE10)
		{
			$layout     = $this->getLayout();
			$filetofind = $this->_createFileName('template', array('name' => $layout . "-msie"));
			if (Path::find($this->_path['template'], $filetofind))
			{
				$this->setLayout($layout . "-msie");
			}
			parent::display($tpl);
		}
		else
		{
			echo LayoutHelper::render('gslframework.header');
			ob_start();
			parent::display($tpl);
			$html = ob_get_clean();

			// Convert what we can of Bootstrap to uikit using regexp
			// Clumsy!!
			if ($params->get('framework', 'bootstrap') == 'uikit3')
			{
				$html = str_replace('class="icon-print"', ' data-uk-icon="print" class="uk-icon" ', $html);
			}
			echo $html;
			echo LayoutHelper::render('gslframework.footer');
		}

	}

	function displaytemplate($tpl = null)
	{

		return parent::display($tpl);

	}

	/**
	 * This method creates a standard cpanel button
	 *
	 * @param unknown_type $link
	 * @param unknown_type $image
	 * @param unknown_type $text
	 */
	function _quickiconButton($link, $image, $text, $path = '/administrator/images/', $target = '', $onclick = '')
	{

		if ($target != '')
		{
			$target = 'target="' . $target . '"';
		}
		if ($onclick != '')
		{
			$onclick = 'onclick="' . $onclick . '"';
		}
		if ($path === null || $path === '')
		{
			$path = '/administrator/images/';
		}
		$alttext = str_replace("<br/>", " ", $text);
		?>
		<div style="float:left;">
			<div class="icon">
				<a href="<?php echo $link; ?>" <?php echo $target; ?>  <?php echo $onclick; ?>
				   title="<?php echo $alttext; ?>">
					<?php
					//echo HTMLHelper::_('image.administrator', $image, $path, NULL, NULL, $text );
					if (strpos($path, '/') === 0)
					{
						$path = StringHelper::substr($path, 1);
					}
					echo HTMLHelper::_('image', $path . $image, $alttext, array('title' => $alttext), false);
					//HTMLHelper::_('image', 'mod_languages/'.$menuType->image.'.gif', $alt, array('title'=>$menuType->title_native), true)
					?>
					<span><?php echo $text; ?></span>
				</a>
			</div>
		</div>
		<?php

	}

	function _quickiconButtonWHover($link, $image, $image_hover, $text, $path = '/administrator/images/', $target = '', $onclick = '')
	{

		if ($target != '')
		{
			$target = 'target="' . $target . '"';
		}
		if ($onclick != '')
		{
			$onclick = 'onclick="' . $onclick . '"';
		}
		if ($path === null || $path === '')
		{
			$path = '/administrator/images/';
		}
		$alttext = str_replace("<br/>", " ", $text);
		?>
		<div id="cp_icon_container">
			<div class="cp_icon">
				<a href="<?php echo $link; ?>" <?php echo $target; ?>  <?php echo $onclick; ?>
				   title="<?php echo $alttext; ?>">
					<?php
					//echo HTMLHelper::_('image.administrator', $image, $path, NULL, NULL, $text );
					if (strpos($path, '/') === 0)
					{
						$path = StringHelper::substr($path, 1);
					}
					$atributes = array('title' => $alttext, 'onmouseover' => 'this.src=\'../' . $path . $image_hover . '\'', 'onmouseout' => 'this.src=\'../' . $path . $image . '\'');

					echo HTMLHelper::_('image', $path . $image, $alttext, $atributes, false);
					//HTMLHelper::_('image', 'mod_languages/'.$menuType->image.'.gif', $alt, array('title'=>$menuType->title_native), true)
					?>
					<span><?php echo $text; ?></span>
				</a>
			</div>
		</div>
		<?php

	}

	/**
	 * Creates label and tool tip window as onmouseover event
	 * if label is empty, a (i) icon is used
	 *
	 * @static
	 *
	 * @param $tip      string    tool tip text declaring label
	 * @param $label    string    label text
	 *
	 * @return        string    html string
	 */
	function tip($tip = '', $label = '')
	{


		if (!$tip)
		{
			$str = $label;
		}
		//$tip = htmlspecialchars($tip, ENT_QUOTES);
		//$tip = str_replace('&quot;', '\&quot;', $tip);
		$tip = str_replace("'", "&#039;", $tip);
		$tip = str_replace('"', "&quot;", $tip);
		$tip = str_replace("\n", " ", $tip);
		if (!$label)
		{
			$str = HTMLHelper::_('tooltip', $tip, null, 'tooltip.png', null, null, 0);
		}
		else
		{
			$str = '<span class="editlinktip">'
				. HTMLHelper::_('tooltip', $tip, $label, null, $label, '', 0)
				. '</span>';
		}

		return $str;

	}

	/**
	 * Loads event editing layout using template
	 */
	function loadEditFromTemplate($template_name = 'icalevent.edit_page', $event = null, $mask = null, $search = array(), $replace = array(), $blank = array())
	{
		static $processedCss = array();
		static $processedJs = array();

		$jevparams = $params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		$app    = Factory::getApplication();

		$datamodel = new JEventsDataModel();
		$datamodel->setupComponentCatids();
		$accessiblecats = explode(",", $datamodel->accessibleCategoryList());

		$db = Factory::getDbo();

		static $allcatids;
		if (!isset($allcatids))
		{
			$query = $db->getQuery(true);

			$query->select('a.id, a.parent_id, a.level');
			$query->from('#__categories AS a');
			$query->where('a.parent_id > 0');

			// Filter on extension.
			$query->where('a.extension = "com_jevents"');
			$query->where('a.published = 1');
			$query->where('a.language in (' . $db->quote(Factory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
			$query->order('a.lft');

			$db->setQuery($query);
			$allcatids = $db->loadObjectList('id');
		}
		$topLevelAccessibleCats = array();
		foreach ($accessiblecats as $accessiblecat)
		{
			if (array_key_exists($accessiblecat, $allcatids ) && $allcatids[$accessiblecat]->level == 1)
			{
				$topLevelAccessibleCats[] = $accessiblecat;
			}
		}
		$secondLevelAccessibleCats = array();
		foreach ($accessiblecats as $accessiblecat)
		{
			if (array_key_exists($accessiblecat, $allcatids ) && $allcatids[$accessiblecat]->level == 2)
			{
				$secondLevelAccessibleCats[] = $accessiblecat;
			}
		}
		$thirdLevelAccessibleCats = array();
		foreach ($accessiblecats as $accessiblecat)
		{
			if (array_key_exists($accessiblecat, $allcatids ) && $allcatids[$accessiblecat]->level == 3)
			{
				$thirdLevelAccessibleCats[] = $accessiblecat;
			}
		}


		// find published template
		static $templates;
		static $fieldNameArray;
		if (!isset($templates))
		{
			$templates      = array();
			$fieldNameArray = array();
			$rawtemplates   = array();
		}
		$specialmodules = false;
		static $allcat_catids;
		$loadedFromFile = false;

		if (!array_key_exists($template_name, $templates))
		{

			$db->setQuery("SELECT * FROM #__jev_defaults WHERE state=1 AND name= " . $db->Quote($template_name) . " AND value<>'' AND " . 'language in (' . $db->quote(Factory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
			$rawtemplates              = $db->loadObjectList();
			$templates[$template_name] = array();
			if ($rawtemplates)
			{
				foreach ($rawtemplates as $rt)
				{
					if (!isset($templates[$template_name][$rt->language]))
					{
						$templates[$template_name][$rt->language] = array();
					}
					$templates[$template_name][$rt->language][$rt->catid] = $rt;
				}
			}

			if (!isset($templates[$template_name]['*'][0]))
			{
				$templatefile = JEV_ADMINPATH . "views/defaults/tmpl/$template_name.html";
				/*
								// Fall back to html version
								// Can't do this because we may need one tab editing or cal before desc etc.
								if (File::exists($templatefile))
								{
									$loadedFromFile = true;
									if (!isset($templates[$template_name]['*']))
									{
										$templates[$template_name]['*'] = array();
									}
									$templates[$template_name]['*'][0]        = new stdClass();
									$templates[$template_name]['*'][0]->value = file_get_contents($templatefile);

									$templateparams = new stdClass();
									// is there custom css or js - if so push into the params
									if (strpos($templates[$template_name]['*'][0]->value, '{{CUSTOMJS}') !== false)
									{
										preg_match('|' . preg_quote('{{CUSTOMJS}}') . '(.+?)' . preg_quote('{{/CUSTOMJS}}') . '|s', $templates[$template_name]['*'][0]->value, $matches);

										if (count($matches) == 2)
										{
											$templateparams->customjs                 = $matches[1];
											$templates[$template_name]['*'][0]->value = str_replace($matches[0], "", $templates[$template_name]['*'][0]->value);
										}
									}
									if (strpos($templates[$template_name]['*'][0]->value, '{{CUSTOMCSS}') !== false)
									{
										preg_match('|' . preg_quote('{{CUSTOMCSS}}') . '(.+?)' . preg_quote('{{/CUSTOMCSS}}') . '|s', $templates[$template_name]['*'][0]->value, $matches);

										if (count($matches) == 2)
										{
											$templateparams->customcss                = $matches[1];
											$templates[$template_name]['*'][0]->value = str_replace($matches[0], "", $templates[$template_name]['*'][0]->value);
										}
									}
									if (isset($templateparams->customcss) && !empty($templateparams->customcss))
									{
										if (!in_array($templateparams->customcss, $processedCss))
										{
											$processedCss[] = $templateparams->customcss;
											//Factory::getDocument()->addStyleDeclaration($templateparams->customcss);
										}
									}
									if (isset($templateparams->customjs) && !empty($templateparams->customjs))
									{
										if (!in_array($templateparams->customjs, $processedJs))
										{
											$processedJs[] = $templateparams->customjs;
											Factory::getDocument()->addScriptDeclaration($templateparams->customjs);
										}
									}

									$templates[$template_name]['*'][0]->params   = json_encode($templateparams);
									$templates[$template_name]['*'][0]->fromfile = true;
								}
								else
								{
									return false;
								}
				*/
			}

			if (isset($templates[$template_name][Factory::getLanguage()->getTag()]))
			{
				$templateArray = $templates[$template_name][Factory::getLanguage()->getTag()];
				// We have the most specific by language now fill in the gaps
				if (isset($templates[$template_name]["*"]))
				{
					foreach ($templates[$template_name]["*"] as $cat => $cattemplates)
					{
						if (!isset($templateArray[$cat]))
						{
							$templateArray[$cat] = $cattemplates;
						}
					}
				}
				$templates[$template_name] = $templateArray;
			}
			else if (isset($templates[$template_name]["*"]))
			{
				$templates[$template_name] = $templates[$template_name]["*"];
			}
			else if (is_array($templates[$template_name]) && count($templates[$template_name]) == 0)
			{
				$templates[$template_name] = array();
			}
			else if (is_array($templates[$template_name]) && count($templates[$template_name]) > 0)
			{
				$templates[$template_name] = current($templates[$template_name]);
			}
			else
			{
				$templates[$template_name] = array();
			}

			$matched = false;
			foreach (array_keys($templates[$template_name]) as $catid)
			{
				if ($templates[$template_name][$catid]->value != "")
				{

					if (isset($templates[$template_name][$catid]->params))
					{
						$templates[$template_name][$catid]->params = new JevRegistry($templates[$template_name][$catid]->params);
						$specialmodules                            = $templates[$template_name][$catid]->params;

					}

					// Adjust template_value to include dynamic module output then strip it out afterwards
					if ($specialmodules)
					{
						$modids = $specialmodules->get("modid", array());
						if (count($modids) > 0)
						{
							$modvals = $specialmodules->get("modval", array());
							// not sure how this can arise :(
							if (is_object($modvals))
							{
								$modvals = get_object_vars($modvals);
							}
							$modids  = array_values($modids);
							$modvals = array_values($modvals);

							for ($count = 0; $count < count($modids) && $count < count($modvals) && trim($modids[$count]) != ""; $count++)
							{
								$templates[$template_name][$catid]->value .= "{{module start:MODULESTART#" . $modids[$count] . "}}";
								// cleaned later!
								//$templates[$template_name][$catid]->value .= preg_replace_callback('|{{.*?}}|', 'cleanLabels', $modvals[$count]);
								$templates[$template_name][$catid]->value .= $modvals[$count];
								$templates[$template_name][$catid]->value .= "{{module end:MODULEEND}}";
							}
						}
					}

					// strip carriage returns other wise the preg replace doesn;y work - needed because wysiwyg editor may add the carriage return in the template field
					$templates[$template_name][$catid]->value = str_replace("\r", '', $templates[$template_name][$catid]->value);
					$templates[$template_name][$catid]->value = str_replace("\n", '', $templates[$template_name][$catid]->value);
					// non greedy replacement - because of the ?
					$templates[$template_name][$catid]->value = preg_replace_callback('|{{.*?}}|', array($this, 'cleanEditLabels'), $templates[$template_name][$catid]->value);

					// Make sure hidden fields and javascript are all loaded
					if (strpos($templates[$template_name][$catid]->value, "{{HIDDENINFO}}") === false)
					{
						$templates[$template_name][$catid]->value .= "{{HIDDENINFO}}";
					}
					$matchesarray = array();
					preg_match_all('|{{.*?}}|', $templates[$template_name][$catid]->value, $matchesarray);

					$templates[$template_name][$catid]->matchesarray = $matchesarray;
				}
			}
		}

		if (is_null($templates[$template_name]))
		{
			return false;
		}

		$catids = $eventCatids = ($event->catids() && count($event->catids())) ? $event->catids() : array($event->catid() ? $event->catid() : 0);
		if ($catids == array(0))
		{
			$catids =  $accessiblecats;
		}
		if (!in_array(0, $catids))
		{
			$catids[] = 0;
		}

		// find the overlap
		$catids = array_values(array_intersect($catids, array_keys($templates[$template_name])));

		// If no categories match - check for parent, one level
		if (count($catids) == 0 || (count($catids) == 1 && $catids[0] == 0))
		{
			$catids   = ($event->catids() && count($event->catids())) ? $event->catids() : array($event->catid());
			$catcount = count($catids);
			for ($c = 0; $c < $catcount; $c++)
			{
				if (isset($allcatids[$catids[$c]]) && $allcatids[$catids[$c]]->parent_id > 0)
				{
					$catids[] = $allcatids[$catids[$c]]->parent_id;
				}
			}
			$catids[] = 0;
			// find the overlap
			$catids = array_values(array_intersect($catids, array_keys($templates[$template_name])));

			// If no categories match - check for parent, one level
			if (count($catids) == 0 || (count($catids) == 1 && $catids[0] == 0))
			{
				$catids   = ($event->catids() && count($event->catids())) ? $event->catids() : array($event->catid());
				$catcount = count($catids);
				for ($c = 0; $c < $catcount; $c++)
				{
					if (isset($allcatids[$catids[$c]]) && $allcatids[$catids[$c]]->parent_id > 0)
					{
						$catids[] = $allcatids[$catids[$c]]->parent_id;
					}
				}
				$catids[] = 0;
				// find the overlap
				$catids = array_values(array_intersect($catids, array_keys($templates[$template_name])));
			}
		}

		// At present must be an EXACT category match - no inheriting allowed!
		if (count($catids) == 0)
		{
			if (!isset($templates[$template_name][0]) || $templates[$template_name][0]->value == "")
			{
				return false;
			}
		}

		$template = false;
		$matchingTemplates = array();
		foreach ($catids as $catid)
		{
			// use the first matching non-empty layout
			if ($templates[$template_name][$catid]->value != "")
			{
				$matchingTemplates[(int) $catid]  = $templates[$template_name][$catid];
			}
		}

		// Existing Event
		if ($eventCatids !== array(0))
		{
			$overlapCatids = array_intersect($eventCatids, array_keys($matchingTemplates));
			if (count($overlapCatids) == 1)
			{
				// first category specific layout that matches
				$template = $matchingTemplates[$overlapCatids[0]];
			}
			// otherwise use the default
			else if (isset($matchingTemplates[0]))
			{
				$template = $matchingTemplates[0];
			}
		}
		// new event
		else if (count($matchingTemplates) >= 1 && count($topLevelAccessibleCats) >= 1)
		{
			$overlapCatids = array_intersect($topLevelAccessibleCats, array_keys($matchingTemplates));
			if (count($overlapCatids) == 1)
			{
				// first category specific layout that matches
				$template = $matchingTemplates[$overlapCatids[0]];
			}
			// otherwise use the default
			else if (isset($matchingTemplates[0]))
			{
				$template = $matchingTemplates[0];
			}
		}
		else if (count($matchingTemplates) >= 1 && count($secondLevelAccessibleCats) >= 1)
		{
			$overlapCatids = array_intersect($secondLevelAccessibleCats, array_keys($matchingTemplates));
			if (count($overlapCatids) == 1)
			{
				// first category specific layout that matches
				$template = $matchingTemplates[$overlapCatids[0]];
			}
			// otherwise use the default
			else if (isset($matchingTemplates[0]))
			{
				$template = $matchingTemplates[0];
			}
		}
		else if (count($matchingTemplates) >= 1 && count($thirdLevelAccessibleCats) >= 1)
		{
			$overlapCatids = array_intersect($thirdLevelAccessibleCats, array_keys($matchingTemplates));
			if (count($overlapCatids) == 1)
			{
				// first category specific layout that matches
				$template = $matchingTemplates[$overlapCatids[0]];
			}
			// otherwise use the default
			else if (isset($matchingTemplates[0]))
			{
				$template = $matchingTemplates[0];
			}
		}
		// otherwise use the default
		else if (isset($matchingTemplates[0]))
		{
			$template = $matchingTemplates[0];
		}

		if (!$template)
		{
			return false;
		}

		$template_value = $template->value;
		$specialmodules = $template->params;

		$matchesarray   = $template->matchesarray;
		$loadedFromFile = isset($template->fromfile);

		$customcss = $template->params->get('customcss', '');
		if (!in_array($customcss, $processedCss))
		{
			$processedCss[] = $customcss;
			Factory::getDocument()->addStyleDeclaration($customcss);
		}

		$customjs = $template->params->get('customjs', '');
		if (!in_array($customjs, $processedJs))
		{
			$processedJs[] = $customjs;
			Factory::getDocument()->addScriptDeclaration($customjs);
		}


		// Create the tabs content
		if (GSLMSIE10  || (!$app->isClient('administrator') && $params->get("newfrontendediting", 1)))
		{
		}
		else
		{
			// replace bootstrap span styling!
			$template_value = str_replace(array('span2', 'span10'), array('gsl-width-1-6', 'gsl-width-5-6'), $template_value);
		}

		// now replace the fields

		$jevparams = ComponentHelper::getParams(JEV_COM_COMPONENT);

		$matchesarrayCount = count($matchesarray[0]);
		for ($i = 0; $i < $matchesarrayCount; $i++)
		{
			$strippedmatch = preg_replace('/(#|:)+[^}]*/', '', $matchesarray[0][$i]);

			if (in_array($strippedmatch, $search))
			{
				continue;
			}
			// translation string
			if (strpos($strippedmatch, "{{_") === 0 && strpos($strippedmatch, " ") === false)
			{
				$search[]      = $strippedmatch;
				$strippedmatch = StringHelper::substr($strippedmatch, 3, StringHelper::strlen($strippedmatch) - 5);
				$replace[]     = Text::_($strippedmatch);
				$blank[]       = "";
				continue;
			}
			// Built in fields
			// can implement special handlers here!
			/*
			  switch ($strippedmatch) {
			  case "{{TITLE}}":
			  $search[] = "{{TITLE}}";
			  $replace[] = $event->title();
			  $blank[] = "";
			  break;
			  default:
			  $strippedmatch = str_replace(array("{", "}"), "", $strippedmatch);
			  if (is_callable(array($event, $strippedmatch)))
			  {
			  $search[] = "{{" . $strippedmatch . "}}";
			  $replace[] = $event->$strippedmatch();
			  $blank[] = "";
			  }
			  break;
			  }
			 */
		}

		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);

		// Close all the tabs in Joomla > 3.0
		$tabstartarray = array();
		$tabstartarray0Count = 0;
		$tabreplace = "";
		preg_match_all('|{{TABSTART#(.*?)}}|', $template_value, $tabstartarray);
		if ($tabstartarray && count($tabstartarray) == 2)
		{
			$tabstartarray0Count = count($tabstartarray[0]);
			if ($tabstartarray0Count > 0)
			{

				if (GSLMSIE10 || (!$app->isClient('administrator') && !$params->get("newfrontendediting", 1)))
				{
					/*
					//We get and add all the tabs
					$tabreplace = '<ul class="nav nav-tabs" id="myEditTabs">';
					for ($tab = 0; $tab < $tabstartarray0Count; $tab++)
					{
						$paneid   = str_replace(" ", "_", htmlspecialchars($tabstartarray[1][$tab]));
						$tablabel = ($paneid == Text::_($paneid)) ? $tabstartarray[1][$tab] : Text::_($paneid);
						if ($tab == 0)
						{
							$tabreplace .= '<li class="active" id="tab' . $paneid . '" ><a data-toggle="tab" href="#' . $paneid . '">' . $tablabel . '</a></li>';
						}
						else
						{
							$tabreplace .= '<li  id="tab' . $paneid . '"><a data-toggle="tab" href="#' . $paneid . '">' . $tablabel . '</a></li>';
						}
					}
					$tabreplace .= "</ul>\n";
					$tabreplace = $tabreplace . $tabstartarray[0][0];
					$template_value = str_replace($tabstartarray[0][0], $tabreplace, $template_value);
					*/
				}
				else
				{
					//We get and add all the tabs
					$tabreplace = '<ul  id="myEditTabs" class="gsl-tab" gsl-tab>';
					for ($tab = 0; $tab < $tabstartarray0Count; $tab++)
					{
						$paneid   = str_replace(" ", "_", htmlspecialchars($tabstartarray[1][$tab]));
						$tablabel = ($paneid == Text::_($paneid)) ? $tabstartarray[1][$tab] : Text::_($paneid);
						if ($tab == 0)
						{
							$tabreplace .= '<li class="gsl-active"><a href="#' . $paneid . '">' . $tablabel . '</a></li>';
						}
						else
						{
							$tabreplace .= '<li><a href="#' . $paneid . '">' . $tablabel . '</a></li>';
						}
					}
					$tabreplace .= "</ul>\n";
				}
			}
		}
		// Create the tabs content
		if ( GSLMSIE10  || (!$app->isClient('administrator') && !$params->get("newfrontendediting", 1)))
		{
			if ($tabstartarray0Count > 0 && isset($tabstartarray[0]))
			{
				for ($tab = 0; $tab < $tabstartarray0Count; $tab++)
				{
					$paneid = str_replace(" ", "_", htmlspecialchars($tabstartarray[1][$tab]));
					$tablabel = ($paneid == Text::_($paneid)) ? $tabstartarray[1][$tab] : Text::_($paneid);
					$tablabel = (strtoupper($tablabel) === $tablabel) ? Text::_($tablabel) : $tablabel;
					if ($tab == 0)
					{
						$tabcode = HTMLHelper::_('bootstrap.startTabSet', 'myEditTabs', array('active' => $paneid))
							. HTMLHelper::_('bootstrap.addTab', "myEditTabs", $paneid, $tablabel);
					}
					else
					{
						$tabcode = HTMLHelper::_('bootstrap.endTab')
							. HTMLHelper::_('bootstrap.addTab', "myEditTabs", $paneid, $tablabel);
					}
					$template_value = str_replace($tabstartarray[0][$tab], $tabcode, $template_value);
				}
				// Manually close the tabs
				$template_value = str_replace("{{TABSEND}}", HTMLHelper::_('bootstrap.endTab')
					. HTMLHelper::_('bootstrap.endTabSet'), $template_value);
			}
		}
		else
		{
			if ($tabstartarray0Count > 0 && isset($tabstartarray[0]))
			{
				$tabstartarray[2] = array();
				for ($tab = 0; $tab < $tabstartarray0Count; $tab++)
				{
					$paneid = str_replace(" ", "_", htmlspecialchars($tabstartarray[1][$tab]));

					$tabcontent = substr($template_value, strpos($template_value, $tabstartarray[0][$tab]) + strlen( $tabstartarray[0][$tab]));
					if ($tab+1 < $tabstartarray0Count)
					{
						$tabcontent = substr($tabcontent, 0, strpos($tabcontent, $tabstartarray[0][$tab+1]));
					}
					else
					{
						$tabcontent = substr($tabcontent, 0, strpos($tabcontent,'{{TABSEND}}'));
					}
					if ($tab == 0)
					{
						$tabcontent = '<li class="gsl-active">' . $tabcontent . '</li>';
					}
					else
					{
						$tabcontent = '<li>' . $tabcontent . '</li>';
					}
					$tabstartarray[2][] = $tabcontent;
				}
				$tabs = '<ul class="gsl-switcher gsl-margin" style="padding-left:40px;">' . implode('', $tabstartarray[2]) . "</ul>";

				// Inject the tabs
				$tabs = $tabreplace . $tabs;

				$template_start = substr($template_value, 0, strpos($template_value, $tabstartarray[0][0]));
				$template_end = substr($template_value, strpos($template_value, "{{TABSEND}}") + 11);
				$template_value = $template_start . $tabs . $template_end;

			}

		}



		// Now do the plugins
		// get list of enabled plugins
		/*
		  $layout = "edit";

		  $jevplugins = PluginHelper::getPlugin("jevents");

		  foreach ($jevplugins as $jevplugin)
		  {
		  $classname = "plgJevents" . ucfirst($jevplugin->name);
		  if (is_callable(array($classname, "substitutefield")))
		  {

		  if (!isset($fieldNameArray[$classname])){
		  $fieldNameArray[$classname] = array();
		  }
		  if (!isset($fieldNameArray[$classname][$layout])){

		  //list($usec, $sec) = explode(" ", microtime());
		  //$starttime = (float) $usec + (float) $sec;

		  $fieldNameArray[$classname][$layout] = call_user_func(array($classname, "fieldNameArray"), $layout);

		  //list ($usec, $sec) = explode(" ", microtime());
		  //$time_end = (float) $usec + (float) $sec;
		  //echo  "$classname::fieldNameArray = ".round($time_end - $starttime, 4)."<br/>";
		  }
		  if ( isset($fieldNameArray[$classname][$layout]["values"]))
		  {
		  foreach ($fieldNameArray[$classname][$layout]["values"] as $fieldname)
		  {
		  if (!strpos($template_value, $fieldname)!==false) {
		  continue;
		  }
		  $search[] = "{{" . $fieldname . "}}";
		  if (strpos($fieldname, "_lbl")>0 && isset($this->customfields[str_replace("_lbl","",$fieldname)])){
		  $replace[] = $this->customfields[str_replace("_lbl","",$fieldname)]["label"]."xx";
		  }
		  else if (isset($this->customfields[$fieldname])){
		  $replace[] = $this->customfields[$fieldname]["input"]."yy";
		  }
		  // is the event detail hidden - if so then hide any custom fields too!
		  else if (!isset($event->_privateevent) || $event->_privateevent != 3)
		  {
		  $replace[] = call_user_func(array($classname, "substitutefield"), $event, $fieldname);
		  if (is_callable(array($classname, "blankfield")))
		  {
		  $blank[] = call_user_func(array($classname, "blankfield"), $event, $fieldname);
		  }
		  else
		  {
		  $blank[] = "";
		  }
		  }
		  else
		  {
		  $blank[] = "";
		  $replace[] = "";
		  }
		  }
		  }
		  }
		  }
		 *
		 */
		$searchCount = count($search);
		for ($s = 0; $s < $searchCount; $s++)
		{
			global $tempreplace, $tempevent, $tempsearch, $tempblank;
			$tempreplace    = $replace[$s];
			$tempblank      = $blank[$s];
			$tempsearch     = str_replace("}}", "#", $search[$s]);
			$tempevent      = $event;
			$template_value = preg_replace_callback("|$tempsearch(.+?)}}|", array($this, 'jevSpecialHandling2'), $template_value);
		}

		$template_value = str_replace($search, $replace, $template_value);

		// Final Cleanups
		$template_value = str_replace($matchesarray[0], "", $template_value);

		// non greedy replacement - because of the ?
		$template_value = preg_replace_callback('|{{.*?}}|', array($this, 'cleanUnpublished'), $template_value);

		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);

		// Disable general showon effects if using a customised event editing form
		$template_value = str_replace("data-showon-gsl", "data-showon-gsl-disabled", $template_value);
		$template_value = str_replace("data-showon-2gsl", "data-showon-gsl", $template_value);
        $template_value = str_replace("data-showon=", "data-showon-gsl=", $template_value);

		echo $template_value;

		return true;

	}

	function cleanEditLabels($matches)
	{

		if (count($matches) == 1)
		{
			$parts = explode(":", $matches[0]);
			if (count($parts) > 0)
			{
				if (strpos($matches[0], "://") > 0)
				{
					return "{{" . $parts[count($parts) - 1];
				}
				array_shift($parts);

				return "{{" . implode(":", $parts);
			}

			return "";
		}

		return "";

	}

	function jevSpecialHandling2($matches)
	{

		if (count($matches) == 2 && strpos($matches[0], "#") > 0)
		{
			global $tempreplace, $tempevent, $tempsearch, $tempblank;
			$parts = explode("#", $matches[1]);
			if ($tempreplace == $tempblank)
			{
				if (count($parts) == 2)
				{
					return $parts[1];
				}
				else
					return "";
			}
			else if (count($parts) >= 1)
			{
				return sprintf($parts[0], $tempreplace);
			}
		}
		else
			return "";

	}

	function cleanUnpublished($matches)
	{

		if (count($matches) == 1)
		{
			return "";
		}

		return $matches;

	}

	protected
	function setupEditForm()
	{

		$app    = Factory::getApplication();
		$input  = $app->input;

		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		$jversion = new Joomla\CMS\Version;

		if ($app->isClient('administrator') || $params->get("newfrontendediting", 1))
		{
			HTMLHelper::script('media/com_jevents/js/gslselect.js', array('version' => JEventsHelper::JEvents_Version(false), 'relative' => false), array('defer' => true));
			//HTMLHelper::script('media/com_jevents/js/gslselect.js', array('version' => JEventsHelper::JEvents_Version(false) . base64_encode(rand(0,99999)), 'relative' => false), array('defer' => true));

			$script = <<< SCRIPT
			document.addEventListener('DOMContentLoaded', function () {
				gslselect('#adminForm select:not(.gsl-hidden)');
			});
SCRIPT;
			Factory::getDocument()->addScriptDeclaration($script);

		}
		else if ($params->get("bootstrapchosen", 1))
		{
			if (!$jversion->isCompatible('4.0'))
			{
				HTMLHelper::_('formbehavior.chosen', '#jevents select:not(.notchosen)');
			}
		}

		$uEditor    = Factory::getUser()->getParam('editor',  Factory::getConfig()->get('editor', 'none'));

		$this->editor = \Joomla\CMS\Editor\Editor::getInstance($uEditor);

		// clean any existing cache files
		$cache = Factory::getCache(JEV_COM_COMPONENT);
		$cache->clean(JEV_COM_COMPONENT);

		// Get the form
		$this->form = $this->get('Form');

		/*
		 * Moved to special model
		// Prepare the data
		// Experiment in the use of Form and template override for forms and fields
		Form::addFormPath(JPATH_COMPONENT_ADMINISTRATOR . "/models/forms/");
		$template = Factory::getApplication()->getTemplate();
		Form::addFormPath(JPATH_THEMES."/$template/html/com_jevents/forms");
		//Form::addFieldPath(JPATH_THEMES."/$template/html/com_jevents/fields");

		$xpath = false;
		// leave form control blank since we want the fields as ev_id and not jform[ev_id]
		$this->form = Form::getInstance("jevents.edit.icalevent", 'icalevent', array('control' => '', 'load_data' => false), false, $xpath);
		Form::addFieldPath(JPATH_THEMES."/$template/html/com_jevents/fields");
		*/

		$rowdata = array();
		foreach ($this->row as $k => $v)
		{
			if (strpos($k, "_") === 0)
			{
				$newk = StringHelper::substr($k, 1);
				//$this->row->$newk = $v;
			}
			else
			{
				$newk = $k;
			}
			$rowdata[$newk] = $v;
		}
		// some variables have fieldnames with camel case names in the form
		$rowdata["allDayEvent"]  = $rowdata["alldayevent"];
		$rowdata["contact_info"] = $rowdata["contact"];

		// set creator based on created_by input
		$rowdata["creator"] = $rowdata["created_by"];

		$this->form->bind($rowdata);

		$this->form->setValue("view12Hour", null, $params->get('com_calUseStdTime', 0) ? 1 : 0);

		$this->catid = $this->row->catid();
		if ($this->catid == 0 && $this->defaultCat > 0)
		{
			$this->catid = $this->defaultCat;
		}
		$this->primarycatid = $this->catid;
		$this->form->setValue("primarycatid", null, $this->primarycatid);

		if ($this->row->catids)
		{
			$this->catid = $this->row->catids;
		}

		if (!isset($this->ev_id))
		{
			$this->ev_id = $this->row->ev_id();
		}

		if ($this->editCopy)
		{
			$this->old_ev_id = $this->ev_id;
			$this->ev_id     = 0;
			$this->repeatId  = 0;
			$this->rp_id     = 0;
			unset($this->row->_uid);
			$this->row->id(0);
		}

		$native  = true;
		$thisCal = null;
		if ($this->row->icsid() > 0)
		{
			$thisCal = $this->dataModel->queryModel->getIcalByIcsid($this->row->icsid());
			if (isset($thisCal) && $thisCal->icaltype == 0)
			{
				// note that icaltype = 0 for imported from URL, 1 for imported from file, 2 for created natively
				$native = false;
			}
			else if (isset($thisCal) && $thisCal->icaltype == 1)
			{
				// note that icaltype = 0 for imported from URL, 1 for imported from file, 2 for created natively
				$native = false;
			}
		}

		// Event editing buttons
		$this->form->setValue("jevcontent", null, $this->row->content());
		if ($params->get('com_show_editor_buttons'))
		{
			$this->form->setFieldAttribute("jevcontent", "hide", $params->get('com_editor_button_exceptions'));
		}
		else
		{
			$this->form->setFieldAttribute("jevcontent", "buttons", "false");
		}

		// Make data available to the form
        $jevdata = array();
		$jevdata["catid"]["dataModel"]            = $this->dataModel;
		$jevdata["catid"]["with_unpublished_cat"] = $this->with_unpublished_cat;
		$jevdata["catid"]["repeatId"]             = $this->repeatId;
		$jevdata["catid"]["excats"]               = false;
		if ($input->getCmd("task") == "icalevent.edit" && isset($this->excats))
		{
			$jevdata["catid"]["excats"] = $this->excats;
		}
		$this->form->setValue("catid", null, $this->catid);

		$jevdata["primarycatid"] = $this->primarycatid;

		$jevdata["creator"]["users"] = false;
		if (($input->getCmd("task") == "icalevent.edit" || $input->getCmd("task") == "icalevent.editcopy"
				|| $input->getCmd("jevtask", "") == "icalevent.edit" || $input->getCmd("jevtask", "") == "icalevent.editcopy") && isset($this->users))
		{
			$jevdata["creator"]["users"] = $this->users;
		}

		$jevdata["ics_id"]["clist"]       = $this->clist;
		$jevdata["ics_id"]["clistChoice"] = $this->clistChoice;
		$jevdata["ics_id"]["thisCal"]     = $thisCal;
		$jevdata["ics_id"]["native"]      = $native;
		$jevdata["ics_id"]["nativeCals"]  = $this->nativeCals;

		$jevdata["lockevent"]["offerlock"] = isset($this->offerlock) ? 1 : 0;

		$jevdata["access"]["event"] = $this->row;
		//$jevdata["access"]["glist"] = isset($this->glist) ? $this->glist : false;

		$jevdata["state"]["ev_id"]     = $this->ev_id;
		$jevdata["published"]["ev_id"] = $this->ev_id;

		$jevdata["location"]["event"]     = $this->row;
		$jevdata["publish_up"]["event"]   = $this->row;
		$jevdata["publish_down"]["event"] = $this->row;
		$jevdata["start_time"]["event"]   = $this->row;
		$jevdata["end_time"]["event"]     = $this->row;

		/*
        $jevdatamap = isset($_SESSION['jevdatamap']) ? $_SESSION['jevdatamap'] : new WeakMap();
        $jevdatamap[$this->form] = $jevdata;
        $_SESSION['jevdatamap'] = $jevdatamap;
		*/

        $this->form->jevdata = $jevdata;

        //custom requiredfields selected by the user in configuration
		$requiredFields = $params->get('com_jeveditionrequiredfields', array());

		// replacement values
		$this->searchtags   = array();
		$this->replacetags  = array();
		$this->blanktags    = array();
		$this->requiredtags = array();

		$requiredTags['id']            = "title";
		$requiredTags['default_value'] = "";
		$requiredTags['alert_message'] = Text::_('JEV_ADD_REQUIRED_FIELD', true) . " " . Text::_("JEV_FIELD_TITLE", true);
		$this->requiredtags[]          = $requiredTags;

		$fields = $this->form->getFieldSet();

		foreach ($fields as $key => $field)
		{
			// title, category and calendar are always required
			if ($key === "title" || $key === "catid" || $key === "ics_id")
			{
				$this->form->setFieldAttribute($key, 'required', 1);
			}

			$fieldAttribute = $this->form->getFieldAttribute($key, "layoutfield");

			if ($fieldAttribute)
			{
				if (in_array($fieldAttribute, $requiredFields))
				{
					$this->form->setFieldAttribute($key, 'required', 1);
				}
			}
		}

		$fields = $this->form->getFieldSet();

		foreach ($fields as $key => $field)
		{
			$fieldAttribute = $this->form->getFieldAttribute($key, "layoutfield");

			if ($fieldAttribute)
			{
				$searchtag           = '{{' . $this->form->getFieldAttribute($key, "layoutfield") . "_LBL}}";
				$this->searchtags[]  = $searchtag;
				$this->replacetags[] = $field->label;
				$this->blanktags[]   = "";

				$this->searchtags[]  = '{{' . $fieldAttribute . "}}";
				$this->replacetags[] = $field->input;
				$this->blanktags[]   = "";

				if (in_array($fieldAttribute, $requiredFields))
				{
					$requiredTags['id']            = $key;
					$requiredTags['default_value'] = $this->form->getFieldAttribute($key, "default");
					$requiredTags['alert_message'] = Text::_('JEV_ADD_REQUIRED_FIELD', true) . " " . Text::_("JEV_FIELD_" . $fieldAttribute, true);
					$this->requiredtags[]          = $requiredTags;
				}
			}

		}

		// Plugins CAN BE LAYERED IN HERE - In Joomla 3.0 we need to call it earlier to get the tab titles
		// append array to extratabs keys content, title, paneid
		$this->extraTabs = array();

		$app->triggerEvent('onEventEdit', array(&$this->extraTabs, &$this->row, &$params));

		foreach ($this->extraTabs as $extraTab)
		{
			if (trim($extraTab['content']) == "")
			{
				continue;
			}

			$extraTab['title']   = str_replace(" ", "_", strtoupper($extraTab['title']));
			$this->searchtags[]  = "{{" . $extraTab['title'] . "}}";
			$this->replacetags[] = $extraTab['content'];
			$this->blanktags[]   = "";
			if (Text::_($extraTab['title']) !== $extraTab['title'])
			{
				$this->searchtags[]  = "{{" . Text::_($extraTab['title']) . "}}";
				$this->replacetags[] = $extraTab['content'];
				$this->blanktags[]   = "";
			}
			if (isset($extraTab['rawtitle']))
			{
				$this->searchtags[]  = "{{" . $extraTab['rawtitle'] . "}}";
				$this->replacetags[] = $extraTab['content'];
				$this->blanktags[]   = "";
			}

		}


		// load any custom fields
		$this->customfields = array();
		$res  = $app->triggerEvent('onEditCustom', array(&$this->row, &$this->customfields));

		ob_start();
		foreach ($this->customfields as $key => $val)
		{
			// skip custom fields that are already displayed on other tabs
			if (isset($val["group"]) && $val["group"] != "default")
			{
				continue;
			}
			/*
			static $firstperson = false;
			if (!$firstperson && strpos($key, "people") && $key!=$people && isset($this->customfields["people"])){
				$this->customfields[$key]["input"] = $this->customfields["people"]["label"] . $this->customfields[$key]["input"];
				$firstperson = true;
			}
			 */
			// not ideal it creates duplicate ULS - but if we don't duplicate they may not show
			if (strpos($key, "people") === 0 && $key != "people" && isset($this->customfields["people"]))
			{
				//$this->customfields[$key]["input"] = $this->customfields["people"]["input"] . $this->customfields[$key]["input"];
			}
			$this->searchtags[]  = '{{' . $key . '}}';
			$this->replacetags[] = $this->customfields[$key]["input"];
			$this->blanktags[]   = "";
			$this->searchtags[]  = '{{' . $key . '_lbl}}';
			$this->replacetags[] = $this->customfields[$key]["label"];
			$this->blanktags[]   = "";
			$this->searchtags[]  = '{{' . $key . '_showon}}';
			$this->replacetags[] = (isset($this->customfields[$key]["showon"]) && !empty($this->customfields[$key]["showon"])) ? $this->customfields[$key]["showon"] : "";
			$this->blanktags[]   = "";

			if (in_array($key, $requiredFields))
			{
				if (isset($this->customfields[$key]["default_value"]) && isset($this->customfields[$key]["id_to_check"]))
				{
					$requiredTags['default_value'] = $this->customfields[$key]["default_value"];
					$requiredTags['id']            = $this->customfields[$key]["id_to_check"];
					$requiredTags['alert_message'] = Text::_('JEV_ADD_REQUIRED_FIELD', true) . " " . Text::_($requiredTags['id']);
				}
				/*
				else
				{
					if ($key ==="agenda" || $key ==="minutes")
					{
						$requiredTags['id'] = "custom_".$key;
					}
					else if (preg_match("/image[0-9]{1,2}/", $key) === 1)
					{
							$requiredTags['id'] = "custom_upload_" . $key;
					}
					else
					{
							$requiredTags['id'] = $key;
					}
					$requiredTags['default_value'] = "";

				}*/
				$requiredTags['label'] = $this->customfields[$key]["label"];
				$this->requiredtags[]  = $requiredTags;
			}

			ob_start();
			// this echos the showon
			JEventsHelper::showOnRel($this->form, 'customfields');
			$showon = ob_get_clean();
			if (isset($this->customfields[$key]["showon"]) && !empty($this->customfields[$key]["showon"]) &&  strpos($this->customfields[$key]["showon"], "data-showon-gsl='") !== false)
			{
				// merge a copy for custom fields since for customised layouts we loose the general showon handling!
				$originalShowon = $this->customfields[$key]["showon"];
                $originalShowon2 = str_replace("data-showon-gsl=", "data-showon-2gsl=", $originalShowon);
				$originalShowon = trim($originalShowon);
				$originalShowon = str_replace("data-showon-gsl='", "", $originalShowon);
				$originalShowon = substr($originalShowon, 0, strlen($originalShowon) - 1);
				try
                {
                    $originalShowon = @json_decode( $originalShowon );
                    if ( $originalShowon )
                    {
                        $lastShowon = count( $originalShowon ) - 1;
                        if ( strlen( $showon ) > 0 )
                        {
                            $originalShowon[$lastShowon]->op = "AND";
                            $showon = str_replace("data-showon-gsl='", "", $showon);
                            $showon = substr($showon, 0, strlen($showon) - 1);
                            $showon = @json_decode( $showon );
                            $showon  = array_merge( $showon, $originalShowon );
                        }
                        $showon = json_encode( $showon );

                        $showon = ' data-showon-gsl=\'' . $showon . '\' ';
						// preserve the custom fields version
                        $showon .= ' ' . $originalShowon2;

                        // replace the custom field showon attribute so that direct editing pages pick up the adjusted value
                        $this->customfields[$key]["showon"] = $showon;
                    }
                }
				catch (\Throwable $e)
                {

                }
			}
			?>
			<div class=" gsl-margin-small-top gsl-child-width-1-1 gsl-grid  jevplugin_<?php echo $key; ?>" <?php echo $showon; ?>>
				<div class="gsl-width-1-6@m gsl-width-1-3">
					<label class="control-label "><?php echo $this->customfields[$key]["label"]; ?></label>
				</div>
				<div class="gsl-width-expand">
					<?php echo $this->customfields[$key]["input"]; ?>
				</div>
			</div>
			<?php
		}
		$this->searchtags[]  = "{{CUSTOMFIELDS}}";
		$output              = ob_get_clean();
		$this->replacetags[] = $output;
		$this->blanktags[]   = "";

	}

}
