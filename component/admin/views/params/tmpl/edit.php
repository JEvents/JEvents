<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: edit16.php 2983 2011-11-10 14:02:23Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2017 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html.bootstrap');
// We need to get the params first

//JHtml::_('formbehavior.chosen', '#adminForm select:not(.notchosen)');
JHtml::_('formbehavior.chosen', '#adminForm select.chosen');

use Joomla\String\StringHelper;

$version = JEventsVersion::getInstance();

$haslayouts = false;
foreach (JEV_CommonFunctions::getJEventsViewList() as $viewfile)
{
	$config = JPATH_SITE . "/components/" . JEV_COM_COMPONENT . "/views/" . $viewfile . "/config.xml";
	if (file_exists($config))
	{
		$haslayouts = true;
	}
}
$hasPlugins = false;
$db = JFactory::getDbo();
$query = $db->getQuery(true)
        ->select('folder AS type, element AS name, params, enabled, manifest_cache ')
        ->from('#__extensions')
        // include unpublished plugins
        //->where('enabled = 1')        
        ->where('type =' . $db->quote('plugin'))
        ->where('state IN (0,1)')
        ->where('(folder="jevents" OR element="gwejson" OR element="jevent_embed")')
        ->order('enabled desc, ordering asc');

$jevplugins = $db->setQuery($query)->loadObjectList();
//echo $db->getQuery();
//$jevplugins = JPluginHelper::getPlugin("jevents");
if (count($jevplugins)){
    $hasPlugins = true;
}
?>
<!-- Set Difficulty : -->

<form action="index.php" method="post" name="adminForm" autocomplete="off" id="adminForm">
	<fieldset class='jevconfig'>
		<?php
		// difficulty rating is outside the tabs!
		$fieldSets = $this->form->getFieldsets();
		foreach ($fieldSets as $name => $fieldSet)
		{
			foreach ($this->form->getFieldset($name) as $field)
			{
				if ($field->fieldname == "com_difficulty")
				{
					?>
					<table class="settings_level">
						<tr class=" difficulty1" >
							<?php
								echo  '<td class="paramlist_key"><span class="editlinktip">' . $field->label . '</span></td>';
								echo  '<td class="paramlist_value">' . $field->input . '</td>';
							?>
						</tr>
					</table>
					<?php
				}
			}
		}
		?>
		<legend>
			<?php echo JText::_('JEV_EVENTS_CONFIG'); ?>
		</legend>	

		<ul class="nav nav-list config" id="myParamsTabs">
			<?php
			$fieldSets = $this->form->getFieldsets();
			$first = true;
			foreach ($fieldSets as $name => $fieldSet)
			{
				if ($name == "permissions")
				{
					continue;
				}
				$label = empty($fieldSet->label) ? $name : $fieldSet->label;

				$class = isset($fieldSet->class) ? $fieldSet->class : "";
				if (!empty($fieldSet->difficulty))
				{
					$difficultySetClass = "difficulty" . $fieldSet->difficulty;
					if ($this->component->params->get("com_difficulty", 1) < $fieldSet->difficulty)
					{
						$difficultySetClass .= " hiddenDifficulty";
					}
				}
				else
				{
					$difficultySetClass = "";
				}
				if ($first)
				{
					$first = false;
					$class = " class= 'active $class $difficultySetClass'";
				}
				else
				{
					$class = " class=' $difficultySetClass'";
				}
				?>
				<li <?php echo $class; ?>><a data-toggle="tab" href="#<?php echo $name; ?>"><?php echo JText::_($label); ?></a></li>
				<?php
			}
			/*
			 * Drop Down tabs - but the drop down doesn't get cleared !
			  if ($haslayouts)
			  {
			  ?>
			  <li class="dropdown">
			  <a data-toggle="dropdown"  class="dropdown-toggle"  href="#club_layouts"><?php echo JText::_("CLUB_LAYOUTS"); ?>  <b class="caret"></b></a>
			  <ul class="dropdown-menu">
			  <?php
			  foreach (JEV_CommonFunctions::getJEventsViewList() as $viewfile)
			  {
			  $config = JPATH_SITE . "/components/" . JEV_COM_COMPONENT . "/views/" . $viewfile . "/config.xml";
			  if (file_exists($config))
			  {
			  ?>
			  <li ><a data-toggle="tab" href="#<?php echo $viewfile; ?>"><?php echo $viewfile; ?></a></li>
			  <?php
			  }
			  }
			  ?>
			  </ul>
			  </li>
			  <?php
			  }
			 */
			if ($haslayouts)
			{
				?>
				<li ><a data-toggle="tab" href="#club_layouts"><?php echo JText::_("CLUB_LAYOUTS"); ?></a></li>
				<?php
			}
			if ($hasPlugins)
			{
				?>
				<li ><a data-toggle="tab" href="#plugin_options"><?php echo JText::_("JEV_PLUGIN_OPTIONS"); ?></a></li>
				<?php
			}
			?>
		</ul>

		<?php
		echo JHtml::_('bootstrap.startPane', 'myParamsTabs', array('active' => 'JEV_TAB_COMPONENT'));
		$fieldSets = $this->form->getFieldsets();

		foreach ($fieldSets as $name => $fieldSet)
		{
			if ($name == "permissions")
			{
				continue;
			}
			$label = empty($fieldSet->label) ? $name : $fieldSet->label;
			echo JHtml::_('bootstrap.addPanel', "myParamsTabs", $name);

			$html = array();

			$html[] = '<table class="paramlist admintable" >';

			if (isset($fieldSet->description) && !empty($fieldSet->description))
			{
				$desc = JText::_($fieldSet->description);
				$html[] = '<tr><td class="paramlist_description" colspan="2">' . $desc . '</td></tr>';
			}

			foreach ($this->form->getFieldset($name) as $field)
			{
				if ($field->hidden || $field->fieldname == "com_difficulty")
				{
					continue;
				}

				$maxjoomlaversion = $this->form->getFieldAttribute($field->fieldname, "maxjoomlaversion", false);
				if ( $maxjoomlaversion && version_compare(JVERSION,$maxjoomlaversion , ">")) {
					continue;
				}
				$minjoomlaversion = $this->form->getFieldAttribute($field->fieldname, "minjoomlaversion", false);
				if ( $minjoomlaversion && version_compare(JVERSION,$minjoomlaversion , "<")) {
					continue;
				}
				
				// Hide club update field if no club addons are installed
				//if ($field->fieldname=="clubcode_spacer" || $field->fieldname=="clubcode"){
				//	// disable if no club addons are installed
				//	$plugins = JPluginHelper::getPlugin("jevents");
				//	if (count($plugins)==0 && !$haslayouts){
				//		continue;
				//	}
				//}

				$class = isset($field->class) ? $field->class : "";

				$difficultyClass = "difficulty" . $this->form->getFieldAttribute($field->fieldname, "difficulty");
				if ($this->component->params->get("com_difficulty", 1) < $this->form->getFieldAttribute($field->fieldname, "difficulty"))
				{
					$difficultyClass .= " hiddenDifficulty";
				}

				if (JString::strlen($class) > 0)
				{
					$class = " class='$class $difficultyClass'";
				}
				else
				{
					$class = " class=' $difficultyClass'";
				}

				$html[] = "<tr $class>";
				if (!isset($field->label) || $field->label == "")
				{
					$html[] = '<td class="paramlist_key"><span class="editlinktip">' . $field->label . '</span></td>';
					$html[] = '<td class="paramlist_value">' . $field->input . '</td>';
				}
				else
				{
					$html[] = '<td class="paramlist_value" colspan="2">' . $field->input . '</td>';
				}

				$html[] = '</tr>';
			}

			if ($name == "JEV_PERMISSIONS")
			{
				$name = "permissions";
				foreach ($this->form->getFieldset($name) as $field)
				{
					$class = isset($field->class) ? $field->class : "";

					if (JString::strlen($class) > 0)
					{
						$class = " class='$class'";
					}
					$html[] = "<tr $class>";
					$html[] = '<td class="paramlist_value" colspan="2">' . $field->input . '</td>';

					$html[] = '</tr>';
				}
			}

			$html[] = '</table>';

			echo implode("\n", $html);
			?>

			<?php
			echo JHtml::_('bootstrap.endPanel');
		}

		if ($haslayouts)
		{
			echo JHtml::_('bootstrap.addPanel', "myParamsTabs", "club_layouts");
			?>
			<ul class="nav nav-tabs" id="myLayoutTabs">
				<?php
				$first = false;
				foreach (JEV_CommonFunctions::getJEventsViewList() as $viewfile)
				{
					$config = JPATH_SITE . "/components/" . JEV_COM_COMPONENT . "/views/" . $viewfile . "/config.xml";
					if (file_exists($config))
					{

						if (!$first)
						{
							$first = $viewfile;
							$class = ' class="active"';
						}
						else
						{
							$class = '';
						}
						?>
						<li <?php echo $class; ?>><a data-toggle="tab" href="#<?php echo $viewfile; ?>"><?php echo $viewfile; ?></a></li>
						<?php
					}
				}
				?>
			</ul>
			<?php
			echo JHtml::_('bootstrap.startPane', "myLayoutTabs", array('active' => $first));

			// Now get layout specific parameters
			//JForm::addFormPath(JPATH_COMPONENT ."/views/");
			foreach (JEV_CommonFunctions::getJEventsViewList() as $viewfile)
			{

				$config = JPATH_SITE . "/components/" . JEV_COM_COMPONENT . "/views/" . $viewfile . "/config.xml";
				if (file_exists($config))
				{

					$layoutform = JForm::getInstance("com_jevent.config.layouts." . $viewfile, $config, array('control' => 'jform', 'load_data' => true), true, "/config");
					$layoutform->bind($this->component->params);

					if (JFile::exists(JPATH_ADMINISTRATOR."/manifests/files/$viewfile.xml")){
						$xml = simplexml_load_file(JPATH_ADMINISTRATOR."/manifests/files/$viewfile.xml");
						$layoutname = (string) $xml->name;
						$langfile = 'files_' . str_replace('files_', '', strtolower(JFilterInput::getInstance()->clean((string) $layoutname, 'cmd')));
						$lang = JFactory::getLanguage();
						$lang->load($langfile , JPATH_SITE, null, false, true);
					}

					$fieldSets = $layoutform->getFieldsets();
					$html = array();
					$hasconfig = false;
					foreach ($fieldSets as $name => $fieldSet)
					{
						$html[] = '<div class="paramlist admintable form-horizontal" >';

						if (isset($fieldSet->description) && !empty($fieldSet->description))
						{
							$desc = JText::_($fieldSet->description);
							$html[] = '<div class="paramlist_description" colspan="2">' . $desc . '</div>';
						}

						foreach ($layoutform->getFieldset($name) as $field)
						{
							if ($field->hidden)
							{
								continue;
							}

							$maxjoomlaversion = $this->form->getFieldAttribute($field->fieldname, "maxjoomlaversion", false);
							if ( $maxjoomlaversion && version_compare(JVERSION,$maxjoomlaversion , ">")) {
								continue;
							}
							$minjoomlaversion = $this->form->getFieldAttribute($field->fieldname, "minjoomlaversion", false);
							if ( $minjoomlaversion && version_compare(JVERSION,$minjoomlaversion , "<")) {
								continue;
							}

							$hasconfig = true;
                                                        $html[] = $field->renderField();
                                                        /*
							$class = isset($field->class) ? $field->class : "";

							if (JString::strlen($class) > 0)
							{
								$class = " class='$class'";
							}
							$html[] = "<tr $class>";
							if (!isset($field->label) || $field->label == "")
							{
								$html[] = '<td class="paramlist_key"><span class="editlinktip">' . $field->label . '</span></td>';
								$html[] = '<td class="paramlist_value">' . $field->input . '</td>';
							}
							else
							{
								$html[] = '<td class="paramlist_value" colspan="2">' . $field->input . '</td>';
							}
							$html[] = '</tr>';
                                                         */
						}
						$html[] = '</div>';
					}

					if (!$hasconfig)
					{
						$x = 1;
					}
					if ($hasconfig)
					{
						echo JHtml::_('bootstrap.addPanel', 'myLayoutTabs', $viewfile);
						//echo JHtml::_('bootstrap.addPanel', 'myParamsTabs', $viewfile);

						echo implode("\n", $html);

						echo JHtml::_('bootstrap.endPanel');
						//echo JHtml::_('bootstrap.endPanel');
					}
				}
			}
			echo JHtml::_('bootstrap.endPane', 'myLayoutTabs');
			echo JHtml::_('bootstrap.endPanel');
		}
                
		if ($hasPlugins)
		{                    
			echo JHtml::_('bootstrap.addPanel', "myParamsTabs", "plugin_options");
                        echo JHtml::_('bootstrap.startAccordion', 'myPluginAccordion', array('active' => 'collapsexx', 'parent' => 'plugin_options'));
                        $script = <<<SCRIPT
jQuery(document).ready(function(){    
    jQuery('#myPluginAccordion').on('show', function (evt) {
       jQuery(evt.target).closest('.accordion-group').find(".icon-chevron-right").removeClass("icon-chevron-right").addClass("icon-chevron-down");
    });
    jQuery('#myPluginAccordion').on('hidden', function (evt) {
       jQuery(evt.target).closest('.accordion-group').find(".icon-chevron-down").removeClass("icon-chevron-down").addClass("icon-chevron-right");
    });                                
});                                
SCRIPT;
                         
                        JevHtmlBootstrap::popover('#myPluginAccordion .icon-info' , array("trigger"=>"hover focus", "placement"=>"top", "container"=>"#plugin_options", "delay"=> array( "show"=> 150, "hide"=> 150 )));                        
                        JFactory::getDocument()->addScriptDeclaration($script);
                        
                        $i = 0;
                        foreach ($jevplugins as $plugin)
                        {
                                $config = JPATH_SITE . "/plugins/".$plugin->type."/" . $plugin->name . "/".$plugin->name.".xml";
                                if (file_exists($config))
                                {     
                                    // Load language file
                                    $lang = JFactory::getLanguage();
                                    $langfile = "plg_".$plugin->type."_".$plugin->name.".sys";
                                    $lang->load($langfile , JPATH_ADMINISTRATOR, null, false, true);
                                    $langfile = "plg_".$plugin->type."_".$plugin->name;
                                    $lang->load($langfile , JPATH_ADMINISTRATOR, null, false, true);
                                    
                                    // Now get plugin specific parameters
                                    //JFactory::getApplication()->setUserState('com_plugins.edit.plugin.data', array());
                                    $pluginform = JForm::getInstance("com_jevents.config.plugins." . $plugin->name, $config, array('control' => 'jform_plugin['.$plugin->type.']['.$plugin->name.']', 'load_data' => true), true, "/extension/config/fields");
                                    //$pluginform = JForm::getInstance('com_plugins.plugin', $config, array('control' => 'jform_plugin['.$plugin->name.']', 'load_data' => true), true, "/extension/config/fields");
                                    $pluginparams = new JRegistry($plugin->params);

                                    // Load the whole XML config file to get the plugin name in plain english
                                    $xml = new SimpleXMLElement($config, 0, true);
                                    // TODO Consider adding enabled/disabled method here for plugins inclusing unpublished ones!
                                    // TODO handle unpublished plugins too
                                    
                                    $hasfields = false;
                                    $fieldSets = $pluginform->getFieldsets();
                                    foreach ($fieldSets as $name => $fieldSet)
                                    {
                                            if ($pluginform->getFieldset($name)) {
                                                $hasfields = true;
                                            }
                                    }
                                    $safedesc = JText::_($xml->description, true);
                                    $safename = JText::_($xml->name, true);
                                    
                                    // offer drop down IFF has fields!
                                    if ($hasfields) {
                                        $label =  '<i class="icon-chevron-right"></i> ' .JText::_($xml->name ) ;
                                    }
                                    else {
                                        $label =  '<i class="icon-blank"></i> ' .JText::_($xml->name ) ;
                                    }
                                    if ($safedesc) {
                                        $label .=  '<i class="icon-info-sign icon-info" data-content="<strong>'.$safename."</strong><br/>".$safedesc.'" style="margin-left:10px;font-size:1.2em;"></i> ' ;
                                    }
                                    else {
                                        $label .=  '<i class="icon-blank" style="margin-left:10px"></i> ' ;
                                    }
                                                                        
                                    $checked1 = $plugin->enabled ? 'checked="checked" ' : '';
                                    $checked0 = !$plugin->enabled ? 'checked="checked" ' : '';
                                    $label .= '<fieldset class="btn-group radio"  style="float:right;">'
						. '<input type="radio"  '.$checked1.'  value="1" name="jform_plugin['.$plugin->type.']['.$plugin->name.'][enabled]"  id="jform_plugin_'.$plugin->type.'_'.$plugin->name.'_params_enabled1" class="btn">'
                                                .'<label for="jform_plugin_'.$plugin->type.'_'.$plugin->name.'_params_enabled1" class="btn">'
                                                . JText::_('JENABLED')
                                                . '</label>'
						. '<input type="radio" '.$checked0.' value="0" name="jform_plugin['.$plugin->type.']['.$plugin->name.'][enabled]"  id="jform_plugin_'.$plugin->type.'_'.$plugin->name.'_params_enabled0" class="btn">'
                                                .'<label for="jform_plugin_'.$plugin->type.'_'.$plugin->name.'_params_enabled0" class="btn">'
                                                . JText::_('JDISABLED')
                                                . '</label>'
                                                .'</fieldset>';
                                    
                                    if ($hasfields) {
                                        echo JHtml::_('bootstrap.addSlide', 'myPluginAccordion', JText::_($label), 'collapse' . ($i++));

                                        $fieldSets = $pluginform->getFieldsets();
                                        $html = array();
                                        $hasconfig = false;
                                        foreach ($fieldSets as $name => $fieldSet)
                                        {
                                                if (!$pluginform->getFieldset($name)) {
                                                   continue;
                                                }

                                                $html[] = '<div class="paramlist admintable form-horizontal" >';

                                                if (isset($fieldSet->description) && !empty($fieldSet->description))
                                                {
                                                        $desc = JText::_($fieldSet->description);
                                                        $html[] = '<div class="paramlist_description" colspan="2">' . $desc . '</div>';
                                                }

                                                foreach ($pluginform->getFieldset($name) as $field)
                                                {
                                                        if ($field->hidden)
                                                        {
                                                                continue;
                                                        }

                                                        // Set the value for the form 
                                                        $paramsval = $pluginparams->get($field->fieldname, $field->default);
                                                        if (is_object($paramsval)){
                                                            // Need this for subform to work
                                                            $paramsval = (array) $paramsval;
                                                        }
                                                        $field->setValue ($paramsval);

                                                        $maxjoomlaversion = $this->form->getFieldAttribute($field->fieldname, "maxjoomlaversion", false);
                                                        if ( $maxjoomlaversion && version_compare(JVERSION,$maxjoomlaversion , ">")) {
                                                                continue;
                                                        }
                                                        $minjoomlaversion = $this->form->getFieldAttribute($field->fieldname, "minjoomlaversion", false);
                                                        if ( $minjoomlaversion && version_compare(JVERSION,$minjoomlaversion , "<")) {
                                                                continue;
                                                        }

                                                        if ($field->fieldname=="whitelist"){
                                                            $x = 1;
                                                        }
                                                        
                                                        $hasconfig = true;
                                                        $html[] = $field->renderField();
                                                        /*
                                                        $class = $field->class;

                                                        if (JString::strlen($class) > 0)
                                                        {
                                                                $class = " class='$class'";
                                                        }
                                                        $html[] = "<tr $class>";
                                                        if (!isset($field->label) || $field->label == "")
                                                        {
                                                                $html[] = '<td class="paramlist_key"><span class="editlinktip">' . $field->label . '</span></td>';
                                                                $html[] = '<td class="paramlist_value">' . $field->input . '</td>';
                                                        }
                                                        else
                                                        {
                                                                $html[] = '<td class="paramlist_value" colspan="2">' . $field->input . '</td>';
                                                        }

                                                        $html[] = '</tr>';
                                                         * 
                                                         */
                                                }
                                                $html[] = '</div>';
                                                echo implode("\n", $html);                                    
                                        }
                                        echo JHtml::_('bootstrap.endSlide');                                     
                                    }
                                    else {
                                        ?>
                                        <div class="accordion-group">
                                            <div class="accordion-heading">
                                                <strong>
                                                    <span class="accordion-toggle">
                                                    <?php  echo $label; ?>
                                                    </span>
                                                </strong>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                                else {
                                    //echo $plugin->name;
                                }
                        }
        		echo JHtml::_('bootstrap.endAccordion');
                        echo JHtml::_('bootstrap.endPanel');
		}                
		?>


	</fieldset>

	<input type="hidden" name="id" value="<?php echo $this->component->id; ?>" />
	<input type="hidden" name="component" value="<?php echo $this->component->option; ?>" />
        <input type="hidden" name="jform_title" id="jform_title" value="com_jevents"/>
	<input type="hidden" name="controller" value="component" />
	<input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_('form.token'); ?>
        
</form>



