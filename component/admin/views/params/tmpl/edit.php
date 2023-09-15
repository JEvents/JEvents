<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: edit16.php 2983 2011-11-10 14:02:23Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\String\StringHelper;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Application\CMSApplication;

// We need to get the params first

$jversion = new Joomla\CMS\Version;
if (!$jversion->isCompatible('4.0'))
{
	HTMLHelper::script('media/com_jevents/js/gslselect.js', array('version' => JEventsHelper::JEvents_Version(false), 'relative' => false), array('defer' => true));
	$script = <<< SCRIPT
			document.addEventListener('DOMContentLoaded', function () {
				gslselect('#adminForm select:not(.gsl-hidden)');
			});
SCRIPT;
	Factory::getDocument()->addScriptDeclaration($script);

	//HTMLHelper::_('formbehavior.chosen', '#adminForm select.chosen');
}

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
$db         = Factory::getDbo();
$query      = $db->getQuery(true)
	->select('folder AS type, element AS name, params, enabled, manifest_cache ')
	->from('#__extensions')
	// include unpublished plugins
	//->where('enabled = 1')
	->where('type =' . $db->quote('plugin'))
	->where('state IN (0,1)')
	->where('(folder="jevents" OR element="gwejson" OR element="jevent_embed"  OR element="jevuser" or element="jevcreator"  or element="jevents")')
	->order('enabled desc, ordering asc');

$jevplugins = $db->setQuery($query)->loadObjectList();
//echo $db->getQuery();
//$jevplugins = PluginHelper::getPlugin("jevents");
if (count($jevplugins))
{
	$hasPlugins = true;
}
?>
<!-- Set Difficulty : -->
<div id="jevents">
	<div id="jevents_body">
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
					<div class="settings_level difficulty1 gsl-grid">
						<div class="gsl-width-auto"><span class="editlinktip"><?php echo $field->label;?></span></div>
						<div class="gsl-width-expand"><?php echo $field->input;?></div>
					</div>
					<?php
				}
			}
		}
		?>
		<legend>
			<?php echo Text::_('JEV_EVENTS_CONFIG'); ?>
		</legend>
<div class="gsl-grid  gsl-margin-remove-left">
		<ul class="config gsl-tab gsl-tab-left gsl-margin-right gsl-width-auto gsl-list-divider" id="myParamsTabs" gsl-tab="connect: #jvts-config-tabs">
			<?php
			$fieldSets = $this->form->getFieldsets();
			$first     = true;
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
				<li <?php echo $class; ?>><a href="#<?php echo $name; ?>"><?php echo Text::_($label); ?></a></li>
				<?php
			}
			/*
			 * Drop Down tabs - but the drop down doesn't get cleared !
			  if ($haslayouts)
			  {
			  ?>
			  <li class="dropdown">
			  <a data-toggle="dropdown"  class="dropdown-toggle"  href="#club_layouts"><?php echo Text::_("CLUB_LAYOUTS"); ?>  <b class="caret"></b></a>
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
				<li><a data-toggle="tab" href="#club_layouts"><?php echo Text::_("CLUB_LAYOUTS"); ?></a></li>
				<?php
			}
			if ($hasPlugins)
			{
				?>
				<li><a data-toggle="tab" href="#plugin_options"><?php echo Text::_("JEV_PLUGIN_OPTIONS"); ?></a></li>
				<?php
			}
			?>
		</ul>
        <!-- Tabs themselves //-->
		<div class=" gsl-margin-remove gsl-card-body gsl-card-default gsl-padding gsl-width-expand">
	        <ul class="gsl-switcher" id="jvts-config-tabs">
            <?php

		$fieldSets = $this->form->getFieldsets();

		foreach ($fieldSets as $name => $fieldSet)
		{
			if ($name == "permissions")
			{
				continue;
			}
			$label = empty($fieldSet->label) ? $name : $fieldSet->label;
            ?>
            <li>
                <?php

			$html = array();

			$html[] = '<div class="gsl-width-1-1" >';

			if (isset($fieldSet->description) && !empty($fieldSet->description))
			{
				$desc   = Text::_($fieldSet->description);
				$html[] = '<div  class="gsl-width-1-1 gsl-card gsl-card-default" >' . $desc . '</div>';
			}

			foreach ($this->form->getFieldset($name) as $field)
			{
				if ($field->hidden || $field->fieldname == "com_difficulty")
				{
					continue;
				}

				$maxjoomlaversion = $this->form->getFieldAttribute($field->fieldname, "maxjoomlaversion", false);
				if ($maxjoomlaversion && version_compare(JVERSION, $maxjoomlaversion, ">"))
				{
					continue;
				}
				$minjoomlaversion = $this->form->getFieldAttribute($field->fieldname, "minjoomlaversion", false);
				if ($minjoomlaversion && version_compare(JVERSION, $minjoomlaversion, "<"))
				{
					continue;
				}

				// Hide club update field if no club addons are installed
				//if ($field->fieldname=="clubcode_spacer" || $field->fieldname=="clubcode"){
				//	// disable if no club addons are installed
				//	$plugins = PluginHelper::getPlugin("jevents");
				//	if (count($plugins)==0 && !$haslayouts){
				//		continue;
				//	}
				//}

				$class = isset($field->class) ? $field->class : "";

				$difficultyClass = "difficulty" . $this->form->getFieldAttribute($field->fieldname, "difficulty");
				if ($this->component->params->get("com_difficulty", 1) < $this->form->getFieldAttribute($field->fieldname, "difficulty1"))
				{
					$difficultyClass .= " hiddenDifficulty";
				}

				if (StringHelper::strlen($class) > 0)
				{
					$class = " class='gsl-grid $class $difficultyClass'";
				}
				else
				{
					$class = " class='gsl-grid  $difficultyClass'";
				}

				$showon = "";
				if ($field && $field->showon)
				{
					HTMLHelper::_('jquery.framework');
					JEVHelper::script('showon.js', 'components/' . JEV_COM_COMPONENT . '/assets/js/');

					$showon  = ' data-showon-gsl=\'' .
						json_encode(FormHelper::parseShowOnConditions($field->showon, $field->formControl, $field->group)) . '\'';
				}
				$html[] = "<div $class " . $showon . " >";
				try
				{
					if (strtolower($field->type) == "note" || strtolower($field->type) == "jevinfo")
	                {
		                $html[] = '<div class="gsl-width-1-1" >' . $field->label . "<div>" . $field->input . '<br></div></div>';
	                }
					else if (!isset($field->label) || $field->label == "")
					{
						$html[] = '<div class="gsl-width-1-2"><span class="editlinktip">' . $field->label . '</span></div>'
						. '<div class="gsl-width-1-2">' . $field->input . '</div>';
					}
					else
					{
						$html[] = '<div class="gsl-width-1-1" >' . $field->input . '</div>';
					}
				}
				catch (Throwable $throwable)
				{
					$html[] = '<div class="gsl-width-1-1" >HERE IS THE PROBLEM</div>';

					Factory::getApplication()->enqueueMessage("Problem With Configuration Of " . $field->fieldname . "<br>" . $throwable->getMessage(), CMSApplication::MSG_ERROR);
				}
				$label = $field->label;

				$html[] = '</div>';
			}

			if ($name == "JEV_PERMISSIONS")
			{
				$name = "permissions";
				foreach ($this->form->getFieldset($name) as $field)
				{
					$class = isset($field->class) ? $field->class : "";

					if (StringHelper::strlen($class) > 0)
					{
						$class = " class='$class'";
					}
					$html[] = "<div $class>";
					$html[] = '<div class="gsl-width-1-1" >' . $field->input . '</div>';

					$html[] = '</div>';
				}
			}

			$html[] = '</div>';

			echo implode("\n", $html);
			?>
            </li>
			<?php
		}

		if ($haslayouts)
		{
			?>
            <li>
			<ul class="gsl-tab" gsl-tab="connect: #jvts-theme-tabs" id="myLayoutTabs">
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
						<li <?php echo $class; ?>><a data-toggle="tab"
						                             href="#<?php echo $viewfile; ?>"><?php echo $viewfile; ?></a></li>
						<?php
					}
				}
				?>
			</ul>
            <!-- Tabs themselves //-->
            <div class=" gsl-margin-remove gsl-card-body gsl-card-default gsl-padding gsl-width-expand">
	            <ul class="gsl-switcher" id="jvts-theme-tabs">
		            <?php

			// Now get layout specific parameters
			//Form::addFormPath(JPATH_COMPONENT ."/views/");
			foreach (JEV_CommonFunctions::getJEventsViewList() as $viewfile)
			{
				$config = JPATH_SITE . "/components/" . JEV_COM_COMPONENT . "/views/" . $viewfile . "/config.xml";
				if (!file_exists($config))
				{
					continue;
				}
					?>
		            <li>
			            <?php
				$config = JPATH_SITE . "/components/" . JEV_COM_COMPONENT . "/views/" . $viewfile . "/config.xml";
				if (file_exists($config))
				{

					$layoutform = Form::getInstance("com_jevent.config.layouts." . $viewfile, $config, array('control' => 'jform', 'load_data' => true), true, "/config");
					$layoutform->bind($this->component->params);

					if (File::exists(JPATH_ADMINISTRATOR . "/manifests/files/$viewfile.xml"))
					{
						$xml        = simplexml_load_file(JPATH_ADMINISTRATOR . "/manifests/files/$viewfile.xml");
						$layoutname = (string) $xml->name;
						$langfile   = 'files_' . str_replace('files_', '', strtolower(InputFilter::getInstance()->clean((string) $layoutname, 'cmd')));
						$lang       = Factory::getLanguage();
						$lang->load($langfile, JPATH_SITE, null, false, true);
					}
					else
					{
						$langfile   = 'files_jevents' . $viewfile . 'layout';
						$lang       = Factory::getLanguage();
						$lang->load($langfile, JPATH_SITE, null, false, true);
					}

					$fieldSets = $layoutform->getFieldsets();
					$html      = array();
					$hasconfig = false;
					foreach ($fieldSets as $name => $fieldSet)
					{
						$html[] = '<div class="gsl-width-1-1" >';

						if (isset($fieldSet->description) && !empty($fieldSet->description))
						{
							$desc   = Text::_($fieldSet->description);
							$html[] = '<div  class="gsl-width-1-1 gsl-card gsl-card-default" >' . $desc . '</div>';
						}

						$html[] = '<div class="paramlist admintable form-horizontal" >';

						foreach ($layoutform->getFieldset($name) as $field)
						{
							if ($field->hidden)
							{
								continue;
							}

							$maxjoomlaversion = $this->form->getFieldAttribute($field->fieldname, "maxjoomlaversion", false);
							if ($maxjoomlaversion && version_compare(JVERSION, $maxjoomlaversion, ">"))
							{
								continue;
							}
							$minjoomlaversion = $this->form->getFieldAttribute($field->fieldname, "minjoomlaversion", false);
							if ($minjoomlaversion && version_compare(JVERSION, $minjoomlaversion, "<"))
							{
								continue;
							}

							$hasconfig = true;

							$fieldhtml = $field->renderField();

							// Short cut replacement pending plugin updates!
							$fieldhtml = str_replace('class="row ', 'class="row  gsl-grid gsl-margin-remove ',$fieldhtml );
							$fieldhtml = str_replace('class="span2', 'class="gsl-width-1-6@m gsl-width-1-1 gsl-margin-small-bottom', $fieldhtml );
							$fieldhtml = str_replace(array('class="span10', 'class=" span10'), 'class="gsl-width-expand gsl-margin-small-bottom  ', $fieldhtml );

							$fieldhtml = str_replace('class="control-group"', 'class="gsl-grid"', $fieldhtml);
							$fieldhtml = str_replace('class="control-label"', 'class="gsl-width-1-3"', $fieldhtml);
							$fieldhtml = str_replace('class="controls"', 'class="gsl-width-2-3"', $fieldhtml);


							// Needed to deal with early execution of initTemplate in backend
							//$fieldhtml = str_replace('gsl-button-group', 'gsl-button-group-ysts',$fieldhtml );

							$fieldhtml = str_replace("data-showon=", "data-showon-gsl=", $fieldhtml);

							$html[] = $fieldhtml;

						}
						$html[] = '</div>';
					}

					if (!$hasconfig)
					{
						$x = 1;
					}
					if ($hasconfig)
					{

						echo implode("\n", $html);

					}
				}
				?>
		            </li>
		            <?php
			}
			?>
	            </ul>
            </div>
            </li>
	            <?php
		}

		if ( $hasPlugins)
		{
			// In Joomla 4 without it when the accordion is toggled uikit adds the <div> as a wrapper and causes
			// TinyMCE to loose the content of the IFrame so we can't use uikit accordion - we must cook our own!!

			// gsl-accordion="targets: > *:not(.no-accordion-icon)"
			?>
            <li>
                <ul class="gsl-list-divider gsl-accordion" id="jevPluginSettings" >
            <?php
            $script = <<< SCRIPT
document.addEventListener('DOMContentLoaded', 
function() {
	
	document.querySelectorAll("#jevPluginSettings.gsl-accordion .gsl-accordion-title ").forEach(function(item) {
		var li = item.parentNode;
		li.addEventListener('click', function (evt) {		
			if (!evt.target.classList.contains('gsl-accordion-title'))
			{
				// must not block enable/disable plugin buttons
				return;
			}		
			var clickedLi = evt.target.parentNode;
			var liContent = clickedLi.querySelector('.gsl-accordion-content');
			//liContent.style.transition="display 2s ease"
			evt.preventDefault();
			
			if (liContent.classList.contains('gsl-hidden'))
			{
				liContent.classList.remove('gsl-hidden');
				li.classList.add('gsl-open');
			}
			else 
			{
				liContent.classList.add('gsl-hidden');
				li.classList.remove('gsl-open');
			}
			
			// close the others	
			document.querySelectorAll("#jevPluginSettings.gsl-accordion .gsl-accordion-title ").forEach(function(item) {
				var li2 = item.parentNode; 
				if (li2 != li)
				{
					var li2Content = li2.querySelector('.gsl-accordion-content');
					if (li2Content && !li2Content.classList.contains('gsl-hidden'))
					{
						li2Content.classList.add('gsl-hidden');
						li2.classList.remove('gsl-open');
					}
				}
			});
			
			//li.scrollIntoView(true);
		});
	});
	
});
SCRIPT;
            Factory::getDocument()->addScriptDeclaration($script);

            $settingWarnings = array();

			$i = 0;
			foreach ($jevplugins as $plugin)
			{
				$config = JPATH_SITE . "/plugins/" . $plugin->type . "/" . $plugin->name . "/" . $plugin->name . ".xml";
				if (file_exists($config))
				{
					// Load language file
					$lang     = Factory::getLanguage();
					$langfile = "plg_" . $plugin->type . "_" . $plugin->name . ".sys";
					$lang->load($langfile, JPATH_ADMINISTRATOR, null, false, true);
					$langfile = "plg_" . $plugin->type . "_" . $plugin->name;
					$lang->load($langfile, JPATH_ADMINISTRATOR, null, false, true);

					// Now get plugin specific parameters
					$pluginform = Form::getInstance("com_jevents.config.plugins." . $plugin->name . $plugin->type, $config, array('control' => 'jform_plugin[' . $plugin->type . '][' . $plugin->name . ']', 'load_data' => true), true, "/extension/config/fields");
					$pluginparams = new JevRegistry($plugin->params);

					$hasfields = false;
					$fieldSets = $pluginform->getFieldsets();
					foreach ($fieldSets as $name => $fieldSet)
					{
						if ($pluginform->getFieldset($name))
						{
							$hasfields = true;
						}
					}

					?>
					<li class="gsl-card gsl-card-default gsl-card-hover <?php echo !$hasfields ? "no-accordion-icon" : "";?>" style="position:relative">
					<?php

					// Load the whole XML config file to get the plugin name in plain english
					$xml = new SimpleXMLElement($config, 0, true);
					// TODO Consider adding enabled/disabled method here for plugins inclusing unpublished ones!
					// TODO handle unpublished plugins too

					$safedesc = Text::_($xml->description, true);
					$safename = Text::_($xml->name, true);

					if ($safedesc)
					{
						$popclass = " hasYsPopover";
						$labelinfo = '  data-yspoptitle="' . $safename . '" data-yspopcontent="' . $safedesc . '" ';
						$labelinfo .= ' data-yspopoptions=\'{"mode" : "click, hover", "offset" : 20,"delayHide" : 200, "pos" : "top-left"}\' ';
					}
					else
					{
						$popclass = "";
						$labelinfo = '';
					}

					// offer drop down IFF has fields!
					if ($hasfields)
					{
						$label = '<i gsl-icon="icon:chevron-right" ></i> ' . Text::_($xml->name);
					}
					else
					{
						$label = '<span style="margin-left:25px;" >' . Text::_($xml->name) ."</span>";
					}
					if ($safedesc)
					{
						$label .= '<i gsl-icon="icon:info" style="margin-left:10px;font-size:1.2em;" class="' . $popclass. '" ' . $labelinfo. '></i> ';
					}
					else
					{
						$label .= '';
					}

					$checked1 = $plugin->enabled ? 'checked="checked" ' : '';
					$checked0 = !$plugin->enabled ? 'checked="checked" ' : '';
					$labelextra    = '<div class="gsl-button-group " style="position:absolute;right:30px" >'
						. '<input type="radio"  ' . $checked1 . '  value="1" name="jform_plugin[' . $plugin->type . '][' . $plugin->name . '][enabled]"  id="jform_plugin_' . $plugin->type . '_' . $plugin->name . '_params_enabled1" class="gsl-hidden">'
						. '<label for="jform_plugin_' . $plugin->type . '_' . $plugin->name . '_params_enabled1" class="gsl-button gsl-button-small ' . ($plugin->enabled ? 'gsl-button-primary' : ''). ' ">'
						. Text::_('JENABLED')
						. '</label>'
						. '<input type="radio" ' . $checked0 . ' value="0" name="jform_plugin[' . $plugin->type . '][' . $plugin->name . '][enabled]"  id="jform_plugin_' . $plugin->type . '_' . $plugin->name . '_params_enabled0" class="gsl-hidden">'
						. '<label for="jform_plugin_' . $plugin->type . '_' . $plugin->name . '_params_enabled0" class="gsl-button gsl-button-small ' . ($plugin->enabled ? '' : 'gsl-button-danger'). '">'
						. Text::_('JDISABLED')
						. '</label>'
						. '</div>';

					//$label = JText::_($xml->name);
					if ($hasfields)
					{

						$fieldSets = $pluginform->getFieldsets();
						$html      = array();
						$hasconfig = false;
						foreach ($fieldSets as $name => $fieldSet)
						{
							if (!$pluginform->getFieldset($name))
							{
								continue;
							}

							$html[] = '<div class="paramlist admintable form-horizontal" >';

							if (isset($fieldSet->description) && !empty($fieldSet->description))
							{
								$desc   = Text::_($fieldSet->description);
								$html[] = '<div class="paramlist_description" >' . $desc . '</div>';
							}

							foreach ($pluginform->getFieldset($name) as $field)
							{
								if ($field->hidden)
								{
									continue;
								}

								// Set the value for the form
								$paramsval = $pluginparams->get($field->fieldname, $field->getAttribute('default'));
								if (is_string($paramsval) && strpos($paramsval, "htmlspecialchars()") > 0)
                                {
									if (!isset($settingWarnings[$safename]))
                                    {
                                        $settingWarnings[$safename] = array();
                                    }
                                    $settingWarnings[$safename][] = strip_tags($field->label);
                                    $paramsval = $field->getAttribute('default', '');
                                }
								/*
                                if (is_string($paramsval) && strpos($paramsval, "Deprecated") > 0)
                                {
                                    if (!isset($settingWarnings[$safename]))
                                    {
                                        $settingWarnings[$safename] = array();
                                    }
                                    $settingWarnings[$safename][] = strip_tags($field->label);
                                    $paramsval = $field->getAttribute('default', '');
                                }
								*/
								if (is_object($paramsval))
								{
									// Need this for subform to work
									$paramsval = (array) $paramsval;
								}
								$field->setValue($paramsval);

								$maxjoomlaversion = $this->form->getFieldAttribute($field->fieldname, "maxjoomlaversion", false);
								if ($maxjoomlaversion && version_compare(JVERSION, $maxjoomlaversion, ">"))
								{
									continue;
								}
								$minjoomlaversion = $this->form->getFieldAttribute($field->fieldname, "minjoomlaversion", false);
								if ($minjoomlaversion && version_compare(JVERSION, $minjoomlaversion, "<"))
								{
									continue;
								}

								if ($field->fieldname == "whitelist")
								{
									$x = 1;
								}

								$hasconfig = true;

								try
								{
									$renderField = $field->renderField();
								}
								catch (Throwable $throwable)
								{
									$renderField = $throwable->getMessage();

								}

								$renderField = str_replace('class="control-group"', 'class="gsl-grid"', $renderField);
								$renderField = str_replace('class="control-label"', 'class="gsl-width-1-3"', $renderField);
								$renderField = str_replace('class="controls"', 'class="gsl-width-2-3"', $renderField);
								$html[]    = $renderField;
							}
							$html[] = '</div>';
						}

						?>
						<?php echo $labelextra; ?>
                        <a class="gsl-accordion-title " href="#"  >
	                        <?php echo $label; ?>
                        </a>
                        <div class="gsl-accordion-content gsl-hidden">
							<?php
							$html = implode("\n", $html);
							echo str_replace("data-showon=", "data-showon-gsl=", $html);
							?>
                        </div>
						<?php
					}
					else
					{

						?>
						<?php echo $labelextra; ?>
						<div class="gsl-accordion-title no-accordion-icon" >
							<?php echo $label; ?>
						</div>
						<?php
					}
					?>

					</li>
					<?php
				}
			}
            if (count($settingWarnings))
            {
                $warningMessage  = "<strong>" .  Text::_("COM_JEVENTS_CHECK_PLUGIN_PARAMETERS") . "</strong><br>";
                $warningMessage .=  Text::_("COM_JEVENTS_CHECK_PLUGIN_PARAMETERS_DESC") . "<br><br>";
                array_walk($settingWarnings, function($pluginWarnings, $pluginName) use (& $warningMessage) {
                    $warningMessage .= "<strong>" . $pluginName . "</strong><br>" ;
                    $warningMessage .= implode(", ",  $pluginWarnings) ;
                    $warningMessage .= "<br>";
                });
                Factory::getApplication()->enqueueMessage($warningMessage, "notice");
            }

			?>
                </ul>
            </li>
            <?php
		}
		?>
        </ul>
		</div>
</div>
	</fieldset>

	<input type="hidden" name="id" value="<?php echo $this->component->id; ?>"/>
	<input type="hidden" name="component" value="<?php echo $this->component->option; ?>"/>
	<input type="hidden" name="jform_title" id="jform_title" value="com_jevents"/>
	<input type="hidden" name="controller" value="component"/>
	<input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT; ?>"/>
	<input type="hidden" name="task" value=""/>
	<?php echo HTMLHelper::_('form.token'); ?>

</form>
	</div>
</div>


