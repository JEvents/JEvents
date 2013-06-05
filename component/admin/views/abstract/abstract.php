<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: abstract.php 3229 2012-01-30 12:06:34Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.view');

class JEventsAbstractView extends JViewLegacy
{

	function __construct($config = null)
	{
		parent::__construct($config);
		jimport('joomla.filesystem.file');

		// Lets check if we have editted before! if not... rename the custom file.
		if (JFile::exists(JPATH_SITE . "/components/com_jevents/assets/css/jevcustom.css"))
		{
			// It is definitely now created, lets load it!
			JEVHelper::stylesheet('jevcustom.css', 'components/' . JEV_COM_COMPONENT . '/assets/css/');
		}

		if (JVersion::isCompatible("3.0"))
		{
			JEVHelper::stylesheet('eventsadmin.css', 'components/' . JEV_COM_COMPONENT . '/assets/css/');
		}
		else
		{
			JEVHelper::stylesheet('eventsadmin16.css', 'components/' . JEV_COM_COMPONENT . '/assets/css/');
		}

		$this->_addPath('template', $this->_basePath . '/' . 'views' . '/' . 'abstract' . '/' . 'tmpl');
		// note that the name config variable is ignored in the parent construct!
		if (JVersion::isCompatible("2.5"))
		{
			$theme = JEV_CommonFunctions::getJEventsViewName();
			$this->addTemplatePath(JPATH_BASE . '/' . 'templates' . '/' . JFactory::getApplication()->getTemplate() . '/' . 'html' . '/' . JEV_COM_COMPONENT . '/' . $theme . '/' . $this->getName());

			// or could have used 
			//$this->addTemplatePath( JPATH_BASE.'/'.'templates'.'/'.JFactory::getApplication()->getTemplate().'/'.'html'.'/'.JEV_COM_COMPONENT.'/'.$config['name'] );
		}

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
	 * Routine to hide submenu suing CSS since there are no paramaters for doing so without hiding the main menu
	 *
	 */
	function _hideSubmenu()
	{
		// WHY THE HELL DO THEY BREAK PUBLIC FUNCTIONS !!!
		if (!JVersion::isCompatible("3.0"))
			JHTML::stylesheet('administrator/components/' . JEV_COM_COMPONENT . '/assets/css/hidesubmenu16.css');
		else
			JHTML::stylesheet('hidesubmenu.css', 'administrator/components/' . JEV_COM_COMPONENT . '/assets/css/');

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
						$path = substr($path, 1);
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
	function loadEditFromTemplate($template_name = 'icalevent.edit_page', $event, $mask, $search = array(), 	$replace = array(), $blank = array())
	{
		$db = JFactory::getDBO();
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
			if (strpos($templates[$template_name]->value,"{{HIDDENINFO}}")===false){
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

		//var_dump($matchesarray);

		for ($i = 0; $i < count($matchesarray[0]); $i++)
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
				$strippedmatch = substr($strippedmatch, 3, strlen($strippedmatch) - 5);
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
		
		
		// Now do the plugins
		// get list of enabled plugins

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
						// is the event detail hidden - if so then hide any custom fields too!
						if (!isset($event->_privateevent) || $event->_privateevent != 3)
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
		
		for ($s = 0; $s < count($search); $s++)
		{
			global $tempreplace, $tempevent, $tempsearch, $tempblank;
			$tempreplace = $replace[$s];
			$tempblank = $blank[$s];
			$tempsearch = str_replace("}}", "#", $search[$s]);
			$tempevent = $event;
			$template_value = preg_replace_callback("|$tempsearch(.+?)}}|", array($this, 'jevSpecialHandling2'), $template_value);
		}


		// Close all the tabs in Joomla < 3.0
		if (JVersion::isCompatible("3.0.0")){
			preg_match_all('|{{TABLINK#(.*?)}}|', $template_value, $tablinks);
			if ($tablinks && count($tablinks)==2 && count($tablinks[0])>0){
				for ($tab=0;$tab<count($tablinks[0]);$tab++){
					$paneid = str_replace(" ","_",htmlspecialchars($tablinks[1][$tab]));
					if ($tab==0){
						$tabreplace = '<ul class="nav nav-tabs" id="myEditTabs"><li class="active"><a data-toggle="tab" href="#'.$paneid .'">'. $tablinks[1][$tab]. '</a></li>';
					}
					else {
						$tabreplace = '<li ><a data-toggle="tab" href="#'.$paneid .'">'. $tablinks[1][$tab]. '</a></li>';
					}
					if ($tab==count($tablinks[0])-1){
						$tabreplace.= "</ul>";
					}
					$template_value = str_replace($tablinks[0][$tab], $tabreplace, $template_value  );
				}
			}
				
			// Create the tabs content
			$tabendarray = array();
			preg_match_all('|{{TABBODYEND}}|', $template_value, $tabendarray);
			if (isset($tabendarray[0]) && count($tabendarray[0])>0){
				$tabendreplace = array();
				$tabendsearch = array();
				for ($tab=0;$tab<count($tabendarray[0]);$tab++){
					$te = $tabendarray[0][$tab];
					$tabendsearch[]="|$te|";
					if ($tab==count($tablinks[0])-1){
						$tabendreplace[]= JHtml::_('bootstrap.endPanel'). JHtml::_('bootstrap.endPane', 'myEditTabs');				
					}
					else {
						$tabendreplace[]= JHtml::_('bootstrap.endPanel');
					}					
				}
				$template_value = preg_replace($tabendsearch,$tabendreplace, $template_value, 1);	

				$tabstartarray = array();
				preg_match_all('|{{TABBODYSTART#(.*?)}}|', $template_value, $tabstartarray);
				if (isset($tabstartarray[0]) && count($tabstartarray[0])>0 &&  count($tabstartarray[0])==count($tablinks[0])){
					for ($tab=0;$tab<count($tabstartarray[0]);$tab++){
						$paneid = str_replace(" ","_",htmlspecialchars($tabstartarray[1][$tab]));
						if ($tab ==0){							
							$tabcode = JHtml::_('bootstrap.startPane', 'myEditTabs', array('active' => $paneid)) . JHtml::_('bootstrap.addPanel', "myEditTabs", $paneid);
						}
						else {
							$tabcode = JHtml::_('bootstrap.addPanel', "myEditTabs", $paneid);
						}
						$template_value = str_replace($tabstartarray[0][$tab], $tabcode, $template_value);
					}
				}
			}									
		}
		else {
			// TABLINKS are not relevant before Joomla 3.0
			// non greedy replacement - because of the ?
			$template_value = preg_replace_callback('|{{TABLINK.*?}}|', array($this, 'cleanUnpublished'),  $template_value);		
			
			// close all the tabs
			// close the last one differently though
			$tabendarray = array();
			preg_match_all('|{{TABBODYEND}}|', $template_value, $tabendarray);
			if (isset($tabendarray[0]) && count($tabendarray[0])>0){
				$tabendreplace = array();
				$tabendsearch = array();		
				foreach ($tabendarray[0] as $te){
					$tabendsearch[]="|$te|";
					$tabendreplace[]= "</div></dd>"."\n";	
				}
				$tabendreplace[count($tabendreplace)-1]="</div></dd></dl>"."\n";
				$template_value = preg_replace($tabendsearch,$tabendreplace, $template_value, 1);	

				$tabstartarray = array();
				preg_match_all('|{{TABBODYSTART#.*?}}|', $template_value, $tabstartarray);
				if (isset($tabstartarray[0]) && count($tabstartarray[0])>0){

					$tabcode = JHtml::_('tabs.start', 'tabs')."</dd>"."\n";
					
					$template_value = str_replace($tabstartarray[0][0], $tabcode.$tabstartarray[0][0], $template_value);
				}
			}									
		}
		
		$template_value = str_replace($search, $replace, $template_value);
		
		// Plugins CAN BE LAYERED IN HERE
		/*
		$params =  JComponentHelper::getParams(JEV_COM_COMPONENT);
		// append array to extratabs keys content, title, paneid
		$extraTabs = array();
		$dispatcher = & JDispatcher::getInstance();		
		$dispatcher->trigger('onEventEdit', array(&$extraTabs, &$this->row, &$params), true);
		if (count($extraTabs) > 0)
		{
			foreach ($extraTabs as $extraTab)
			{
				if (!JVersion::isCompatible("3.0.0")){
					$tabContent  = '<dt class="tabs ' . $extraTab['paneid'] . '"><span><h3><a href="javascript:void(0);">' . $extraTab['title'] . '</a></h3></span></dt><dd class="tabs">'."\n";
					$tabContent  .= "<div class='jevextrablock'>"."\n";
					$tabContent  .=  $extraTab['content']."\n";
					$template_value = str_replace("{{TABBODYSTART#".$extraTab['title']."}}",$tabContent, $template_value);	
				}
				else {
				}
								
			}
		}
		*/
		
		// finish off the other tabs
		if (!JVersion::isCompatible("3.0.0")){
			$tabstartarray = array();
			preg_match_all('|{{TABBODYSTART#.*?}}|', $template_value, $tabstartarray);
			if (isset($tabstartarray[0]) && count($tabstartarray[0])>0){
				foreach ($tabstartarray[0] as $ts){
					$title = str_replace(array("{{TABBODYSTART#","}}"),"", $ts);
					$paneid = base64_encode($title);
					$tabContent  = '<dt class="tabs ' . $paneid . '"><span><h3><a href="javascript:void(0);">' . $title . '</a></h3></span></dt><dd class="tabs">'."\n";
					$tabContent  .= "<div class='jevextrablock'>"."\n";
					//$tabContent  .=  $title."\n";
					$template_value = str_replace("{{TABBODYSTART#".$title."}}",$tabContent, $template_value);	
				}
			}						
		}

		//$template_value = str_replace($matchesarray[0], "", $template_value);
		
		// non greedy replacement - because of the ?
		//$template_value = preg_replace_callback('|{{.*?}}|', array($this, 'cleanUnpublished'),  $template_value);		
		
		$params =  JComponentHelper::getParams(JEV_COM_COMPONENT);
		
		
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
	
}
