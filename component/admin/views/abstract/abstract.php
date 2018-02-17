<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: abstract.php 3229 2012-01-30 12:06:34Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2018 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.view');

use Joomla\String\StringHelper;

class JEventsAbstractView extends JViewLegacy
{

	function __construct($config = null)
	{
		parent::__construct($config);
		jimport('joomla.filesystem.file');

		JEVHelper::stylesheet('eventsadmin.css', 'components/' . JEV_COM_COMPONENT . '/assets/css/');

		$this->_addPath('template', $this->_basePath . '/' . 'views' . '/' . 'abstract' . '/' . 'tmpl');
		// note that the name config variable is ignored in the parent construct!

		// Ok getTemplate doesn't seem to get the active menu item's template, so lets do it ourselves if it exists

		$app = JFactory::getApplication();
		// Get current template style ID
		$page_template_id = $app->isAdmin() ? "0" : @$app->getMenu()->getActive()->template_style_id;

		// Check it's a valid style with simple check
		if (!($page_template_id == "" || $page_template_id == "0")) {
			// Load the valid style:
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('template')
				->from('#__template_styles')
				->where('id =' . $db->quote($page_template_id) . '');
			$db->setQuery($query);
			$template = $db->loadResult();

		} else {
			$template = JFactory::getApplication()->getTemplate();
		}

		$theme = JEV_CommonFunctions::getJEventsViewName();
		$name = $this->getName();
		$name = str_replace($theme."/", "", $name);
		$this->addTemplatePath(JPATH_BASE . '/' . 'templates' . '/' . $template . '/' . 'html' . '/' . JEV_COM_COMPONENT . '/' . $theme . '/' . $name);

		// or could have used
		//$this->addTemplatePath( JPATH_BASE.'/'.'templates'.'/'.JFactory::getApplication()->getTemplate().'/'.'html'.'/'.JEV_COM_COMPONENT.'/'.$config['name'] );
		

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
		}

		// Allow the layout to be overriden by menu parameter - this only works if its valid for the task
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);

		// layout may get re-assigned by $this->$layout($tpl); for handle different versions of Joomla
		$layout = $this->getLayout();
		$newlayout = $params->get("overridelayout", $layout);

		// check the template layout is valid for this task
		jimport('joomla.filesystem.path');
		$filetofind = $this->_createFileName('template', array('name' => $newlayout));
		if (JPath::find($this->_path['template'], $filetofind))
		{
			$this->setLayout($newlayout);
		}

		parent::display($tpl);

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
				<a href="<?php echo $link; ?>" <?php echo $target; ?>  <?php echo $onclick; ?> title="<?php echo $alttext; ?>">
					<?php
					//echo JHTML::_('image.administrator', $image, $path, NULL, NULL, $text ); 
					if (strpos($path, '/') === 0)
					{
						$path = JString::substr($path, 1);
					}
					echo JHTML::_('image', $path . $image, $alttext, array('title' => $alttext), false);
					//JHtml::_('image', 'mod_languages/'.$menuType->image.'.gif', $alt, array('title'=>$menuType->title_native), true)
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
				<a href="<?php echo $link; ?>" <?php echo $target; ?>  <?php echo $onclick; ?> title="<?php echo $alttext; ?>">
					<?php
					//echo JHTML::_('image.administrator', $image, $path, NULL, NULL, $text );
					if (strpos($path, '/') === 0)
					{
						$path = JString::substr($path, 1);
					}
					$atributes = array('title' => $alttext, 'onmouseover' => 'this.src=\'../' . $path . $image_hover . '\'', 'onmouseout' => 'this.src=\'../' . $path . $image . '\'' );

					echo JHTML::_('image', $path . $image, $alttext, $atributes, false);
					//JHtml::_('image', 'mod_languages/'.$menuType->image.'.gif', $alt, array('title'=>$menuType->title_native), true)
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
	 * @param $tip	string	tool tip text declaring label
	 * @param $label	string	label text
	 * @return		string	html string
	 */
	function tip($tip = '', $label = '')
	{

		JHTML::_('behavior.tooltip');
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
			$str = JHTML::_('tooltip', $tip, null, 'tooltip.png', null, null, 0);
		}
		else
		{
			$str = '<span class="editlinktip">'
					. JHTML::_('tooltip', $tip, $label, null, $label, '', 0)
					. '</span>';
		}
		return $str;

	}

	/**
	 * Loads event editing layout using template
	 */
	function loadEditFromTemplate($template_name = 'icalevent.edit_page', $event, $mask, $search = array(), $replace = array(), $blank = array())
	{
		$db = JFactory::getDbo();
		// find published template
		static $templates;
		static $fieldNameArray;
		if (!isset($templates))
		{
			$templates = array();
			$fieldNameArray = array();
		}

		if (!array_key_exists($template_name, $templates))
		{
			$db->setQuery("SELECT * FROM #__jev_defaults WHERE state=1 AND name= " . $db->Quote($template_name) . " AND " . 'language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
			$templates[$template_name] = $db->loadObjectList("language");
			if (isset($templates[$template_name][JFactory::getLanguage()->getTag()]))
			{
				$templates[$template_name] = $templates[$template_name][JFactory::getLanguage()->getTag()];
			}
			else if (isset($templates[$template_name]["*"]))
			{
				$templates[$template_name] = $templates[$template_name]["*"];
			}
			else if (is_array($templates[$template_name]) && count($templates[$template_name]) == 0)
			{
				$templates[$template_name] = null;
			}
			else if (is_array($templates[$template_name]))
			{
				$templates[$template_name] = current($templates[$template_name]);
			}
			else
			{
				$templates[$template_name] = null;
			}

			if (is_null($templates[$template_name]) || $templates[$template_name]->value == "")
				return false;

			// strip carriage returns other wise the preg replace doesn;y work - needed because wysiwyg editor may add the carriage return in the template field
			$templates[$template_name]->value = str_replace("\r", '', $templates[$template_name]->value);
			$templates[$template_name]->value = str_replace("\n", '', $templates[$template_name]->value);
			// non greedy replacement - because of the ?
			$templates[$template_name]->value = preg_replace_callback('|{{.*?}}|', array($this, 'cleanEditLabels'), $templates[$template_name]->value);

			// Make sure hidden fields and javascript are all loaded
			if (strpos($templates[$template_name]->value, "{{HIDDENINFO}}") === false)
			{
				$templates[$template_name]->value .= "{{HIDDENINFO}}";
			}
			$matchesarray = array();
			preg_match_all('|{{.*?}}|', $templates[$template_name]->value, $matchesarray);

			$templates[$template_name]->matchesarray = $matchesarray;
		}
		if (is_null($templates[$template_name]) || $templates[$template_name]->value == "")
			return false;

		$template = $templates[$template_name];

		$template_value = $template->value;
		$matchesarray = $templates[$template_name]->matchesarray;

		if ($template_value == "")
			return;
		if (count($matchesarray) == 0)
			return;


		// now replace the fields

		$jevparams = JComponentHelper::getParams(JEV_COM_COMPONENT);

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
				$search[] = $strippedmatch;
				$strippedmatch = JString::substr($strippedmatch, 3, JString::strlen($strippedmatch) - 5);
				$replace[] = JText::_($strippedmatch);
				$blank[] = "";
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

		// Close all the tabs in Joomla > 3.0
		$tabstartarray = array();
		preg_match_all('|{{TABSTART#(.*?)}}|', $template_value, $tabstartarray);
		if ($tabstartarray && count($tabstartarray) == 2)
		{
			$tabstartarray0Count = count($tabstartarray[0]);
			if ($tabstartarray0Count > 0)
			{
				//We get and add all the tabs
				$tabreplace = '<ul class="nav nav-tabs" id="myEditTabs">';
				for ($tab = 0; $tab < $tabstartarray0Count; $tab++)
				{
					$paneid = str_replace(" ", "_", htmlspecialchars($tabstartarray[1][$tab]));
					$tablabel = ($paneid == JText::_($paneid)) ? $tabstartarray[1][$tab] : JText::_($paneid);
					if ($tab == 0)
					{
						$tabreplace .= '<li class="active" id="tab'.$paneid.'" ><a data-toggle="tab" href="#' . $paneid . '">' . $tablabel . '</a></li>';
					}
					else
					{
						$tabreplace .= '<li  id="tab'.$paneid.'"><a data-toggle="tab" href="#' . $paneid . '">' . $tablabel . '</a></li>';
					}
				}
				$tabreplace.= "</ul>\n";
				$tabreplace = $tabreplace . $tabstartarray[0][0];
				$template_value = str_replace($tabstartarray[0][0], $tabreplace, $template_value);
			}
		}
		// Create the tabs content
		if (isset($tabstartarray[0]) && $tabstartarray0Count > 0)
		{
			for ($tab = 0; $tab < $tabstartarray0Count; $tab++)
			{
				$paneid = str_replace(" ", "_", htmlspecialchars($tabstartarray[1][$tab]));
				if ($tab == 0)
				{
					$tabcode = JHtml::_('bootstrap.startPane', 'myEditTabs', array('active' => $paneid)) . JHtml::_('bootstrap.addPanel', "myEditTabs", $paneid);
				}
				else
				{
					$tabcode = JHtml::_('bootstrap.endPanel') . JHtml::_('bootstrap.addPanel', "myEditTabs", $paneid);
				}
				$template_value = str_replace($tabstartarray[0][$tab], $tabcode, $template_value);
			}
			// Manually close the tabs
			$template_value = str_replace("{{TABSEND}}",JHtml::_('bootstrap.endPanel') . JHtml::_('bootstrap.endPane'), $template_value);
		}
	

		// Now do the plugins
		// get list of enabled plugins
		/*
		  $layout = "edit";

		  $jevplugins = JPluginHelper::getPlugin("jevents");

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
			$tempreplace = $replace[$s];
			$tempblank = $blank[$s];
			$tempsearch = str_replace("}}", "#", $search[$s]);
			$tempevent = $event;
			$template_value = preg_replace_callback("|$tempsearch(.+?)}}|", array($this, 'jevSpecialHandling2'), $template_value);
		}

		$template_value = str_replace($search, $replace, $template_value);

		// Final Cleanups
		$template_value = str_replace($matchesarray[0], "", $template_value);

		// non greedy replacement - because of the ?
		$template_value = preg_replace_callback('|{{.*?}}|', array($this, 'cleanUnpublished'), $template_value);

		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);


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
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);

		$this->editor =  JFactory::getEditor();
		if ($this->editor->get("_name") == "codemirror")
		{
			$this->editor = JFactory::getEditor("none");
			JFactory::getApplication()->enqueueMessage(JText::_("JEV_CODEMIRROR_NOT_COMPATIBLE_EDITOR", "WARNING"));
		}

		// clean any existing cache files
		$cache =  JFactory::getCache(JEV_COM_COMPONENT);
		$cache->clean(JEV_COM_COMPONENT);

		/*
		// Get/Create the model
		if ($model =  $this->getModel("icalevent", "icaleventsModel")) {
			// Push the model into the view (as default)
			$this->view->setModel($model, true);
		}
		 */

		// Get the form
		$this->form = $this->get('Form');

		/*
		 * Moved to special model
		// Prepare the data
		// Experiment in the use of JForm and template override for forms and fields
		JForm::addFormPath(JPATH_COMPONENT_ADMINISTRATOR . "/models/forms/");
		$template = JFactory::getApplication()->getTemplate();
		JForm::addFormPath(JPATH_THEMES."/$template/html/com_jevents/forms");
		//JForm::addFieldPath(JPATH_THEMES."/$template/html/com_jevents/fields");

		$xpath = false;
		// leave form control blank since we want the fields as ev_id and not jform[ev_id]
		$this->form = JForm::getInstance("jevents.edit.icalevent", 'icalevent', array('control' => '', 'load_data' => false), false, $xpath);
		JForm::addFieldPath(JPATH_THEMES."/$template/html/com_jevents/fields");
		*/
		
		$rowdata = array();
		foreach ($this->row as $k => $v)
		{
			if (strpos($k, "_") === 0)
			{
				$newk = JString::substr($k, 1);
				//$this->row->$newk = $v;
			}
			else
			{
				$newk = $k;
			}
			$rowdata[$newk] = $v;
		}
		// some variables have fieldnames with camel case names in the form
		$rowdata["allDayEvent"] = $rowdata["alldayevent"];
		$rowdata["contact_info"] = $rowdata["contact"];

		// set creator based on created_by input
		$rowdata["creator"] = $rowdata["created_by"];

		$this->form->bind($rowdata);

		$this->form->setValue("view12Hour", null,$params->get('com_calUseStdTime', 0) ? 1 : 0);

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
			$this->ev_id = 0;
			$this->repeatId = 0;
			$this->rp_id = 0;
			unset($this->row->_uid);
			$this->row->id(0);
		}

		$native = true;
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
		$this->form->jevdata["catid"]["dataModel"] = $this->dataModel;
		$this->form->jevdata["catid"]["with_unpublished_cat"] = $this->with_unpublished_cat;
		$this->form->jevdata["catid"]["repeatId"] = $this->repeatId;
		$this->form->jevdata["catid"]["excats"] = false;
		if (JRequest::getCmd("task") == "icalevent.edit" && isset($this->excats))
		{
			$this->form->jevdata["catid"]["excats"] = $this->excats;
		}
		$this->form->setValue("catid", null, $this->catid);

		$this->form->jevdata["primarycatid"] = $this->primarycatid;

		$this->form->jevdata["creator"]["users"] = false;
		if ((JRequest::getCmd("task") == "icalevent.edit" || JRequest::getCmd("task") == "icalevent.editcopy"
				|| JRequest::getCmd("jevtask") == "icalevent.edit" || JRequest::getCmd("jevtask") == "icalevent.editcopy")  && isset($this->users))
		{
			$this->form->jevdata["creator"]["users"] = $this->users;
		}

		$this->form->jevdata["ics_id"]["clist"] = $this->clist;
		$this->form->jevdata["ics_id"]["clistChoice"] = $this->clistChoice;
		$this->form->jevdata["ics_id"]["thisCal"] = $thisCal;
		$this->form->jevdata["ics_id"]["native"] = $native;
		$this->form->jevdata["ics_id"]["nativeCals"] = $this->nativeCals;

		$this->form->jevdata["lockevent"]["offerlock"] = isset($this->offerlock) ? 1 : 0;

		$this->form->jevdata["access"]["event"] = $this->row;
		//$this->form->jevdata["access"]["glist"] = isset($this->glist) ? $this->glist : false;

		$this->form->jevdata["state"]["ev_id"] = $this->ev_id;
		$this->form->jevdata["published"]["ev_id"] = $this->ev_id;
		
		$this->form->jevdata["location"]["event"] = $this->row;
		$this->form->jevdata["publish_up"]["event"] = $this->row;
		$this->form->jevdata["publish_down"]["event"] = $this->row;
		$this->form->jevdata["start_time"]["event"] = $this->row;
		$this->form->jevdata["end_time"]["event"] = $this->row;

		//custom requiredfields selected by the user in configuration
		$requiredFields = $params->get('com_jeveditionrequiredfields', array());

		// replacement values
		$this->searchtags = array();
		$this->replacetags = array();
		$this->blanktags = array();
		$this->requiredtags = array();
                
		$requiredTags['id'] = "title";
		$requiredTags['default_value'] = "";
		$requiredTags['alert_message'] = JText::_('JEV_ADD_REQUIRED_FIELD',true)." ". JText::_("JEV_FIELD_TITLE",true);
		$this->requiredtags[] = $requiredTags;

		$fields = $this->form->getFieldSet();
		foreach ($fields as $key => $field)
		{
			$fieldAttribute = $this->form->getFieldAttribute($key, "layoutfield");

			if ($fieldAttribute)
			{
				$searchtag = '{{' . $this->form->getFieldAttribute($key, "layoutfield") . "_LBL}}";
				$this->searchtags[] = $searchtag;
				$this->replacetags[] = $field->label;
				$this->blanktags[] = "";

				$this->searchtags[] = '{{' . $fieldAttribute . "}}";
				$this->replacetags[] = $field->input;
				$this->blanktags[] = "";

				if (in_array($fieldAttribute, $requiredFields))
				{
					$requiredTags['id'] = $key;
					$requiredTags['default_value'] = $this->form->getFieldAttribute($key, "default");
					$requiredTags['alert_message'] = JText::_('JEV_ADD_REQUIRED_FIELD',true)." ". JText::_("JEV_FIELD_".$fieldAttribute,true);
					$this->requiredtags[] = $requiredTags;
				}
			}
		}

		// Plugins CAN BE LAYERED IN HERE - In Joomla 3.0 we need to call it earlier to get the tab titles
		// append array to extratabs keys content, title, paneid
		$this->extraTabs = array();
		$dispatcher = JEventDispatcher::getInstance();
		$dispatcher->trigger('onEventEdit', array(&$this->extraTabs, &$this->row, &$params), true);

		foreach ($this->extraTabs as $extraTab)
		{
			if (trim($extraTab['content'])=="") {
				continue;
			}

			$extraTab['title'] = str_replace(" ", "_", strtoupper($extraTab['title']));
			$this->searchtags[] = "{{" . $extraTab['title'] . "}}";
			$this->replacetags[] = $extraTab['content'];
			$this->blanktags[] = "";
			if (JText::_($extraTab['title']) !==$extraTab['title']){
				$this->searchtags[] = "{{" . JText::_($extraTab['title']) . "}}";
				$this->replacetags[] = $extraTab['content'];
				$this->blanktags[] = "";
			}
			if (isset($extraTab['rawtitle'])) {
				$this->searchtags[] = "{{" . $extraTab['rawtitle'] . "}}";
				$this->replacetags[] = $extraTab['content'];
				$this->blanktags[] = "";
			}

		}


		// load any custom fields
		$this->customfields = array();
		$res = $dispatcher->trigger('onEditCustom', array(&$this->row, &$this->customfields));

		ob_start();
		foreach ($this->customfields as $key => $val)
		{
                    // skip custom fields that are already displayed on other tabs
                    if (isset($val["group"]) && $val["group"]!="default"){
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
			if (strpos($key, "people")===0 && $key!="people" && isset($this->customfields["people"])){
				//$this->customfields[$key]["input"] = $this->customfields["people"]["input"] . $this->customfields[$key]["input"];
			}
			$this->searchtags[] = '{{' . $key . '}}';
			$this->replacetags[] = $this->customfields[$key]["input"];
			$this->blanktags[] = "";
			$this->searchtags[] = '{{' . $key . '_lbl}}';
			$this->replacetags[] = $this->customfields[$key]["label"];
			$this->blanktags[] = "";
			$this->searchtags[] = '{{' . $key . '_showon}}';
			$this->replacetags[] = isset($this->customfields[$key]["showon"]) ? $this->customfields[$key]["showon"] : "";
			$this->blanktags[] = "";

			if (in_array($key, $requiredFields))
			{
				if( isset($this->customfields[$key]["default_value"]) && isset($this->customfields[$key]["id_to_check"]) )
				{
					$requiredTags['default_value'] = $this->customfields[$key]["default_value"];
					$requiredTags['id'] = $this->customfields[$key]["id_to_check"];
					$requiredTags['alert_message'] = JText::_('JEV_ADD_REQUIRED_FIELD',true)." ".JText::_($requiredTags['id']);
				}
/*				else
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
				$this->requiredtags[] = $requiredTags;
			}
                        ?>
                        <div class="control-group jevplugin_<?php echo $key; ?>" <?php echo isset($this->customfields[$key]["showon"])?$this->customfields[$key]["showon"]:""; ?>>
                                <label class="control-label "><?php echo $this->customfields[$key]["label"]; ?></label>
                                <div class="controls" >
                                        <?php echo $this->customfields[$key]["input"]; ?>
                                </div>
                        </div>
                        <?php
		}
		$this->searchtags[] = "{{CUSTOMFIELDS}}";
		$output = ob_get_clean();
		$this->replacetags[] = $output;
		$this->blanktags[] = "";

	}

}
