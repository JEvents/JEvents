<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: edit16.php 2983 2011-11-10 14:02:23Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2019 GWE Systems Ltd
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

// We need to get the params first

HTMLHelper::_('formbehavior.chosen', '#adminForm select.chosen');

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
	->where('(folder="jevents" OR element="gwejson" OR element="jevent_embed")')
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
						<tr class=" difficulty1 gsl-grid">
							<td class="gsl-width-auto"><span class="editlinktip"><?php echo $field->label;?></span></td>
							<td class="gsl-width-expand"><?php echo $field->input;?></td>
						</tr>
					</table>
					<?php
				}
			}
		}
		?>
		<legend>
			<?php echo Text::_('JEV_EVENTS_CONFIG'); ?>
		</legend>
<div class="gsl-grid  gsl-margin-remove-left">
		<ul class="config gsl-tab-left gsl-margin-right gsl-width-auto gsl-list-divider" id="myParamsTabs" gsl-tab="connect: #ysts-config-tabs">
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
				<li <?php echo $class; ?>><a ="#<?php echo $name; ?>"><?php echo Text::_($label); ?></a></li>
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
	        <ul class="gsl-switcher" id="ysts-config-tabs">
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

			$html[] = '<table class="paramlist admintable" >';

			if (isset($fieldSet->description) && !empty($fieldSet->description))
			{
				$desc   = Text::_($fieldSet->description);
				$html[] = '<tr><td class="paramlist_description" colspan="2">' . $desc . '</td></tr>';
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
				if ($this->component->params->get("com_difficulty", 1) < $this->form->getFieldAttribute($field->fieldname, "difficulty"))
				{
					$difficultyClass .= " hiddenDifficulty";
				}

				if (StringHelper::strlen($class) > 0)
				{
					$class = " class='$class $difficultyClass'";
				}
				else
				{
					$class = " class=' $difficultyClass'";
				}

				$html[] = "<tr $class>";
				if (strtolower($field->type) == "note")
                {
	                $html[] = '<td class="paramlist_value" colspan="2">' . $field->label . "<div>" . $field->input . '<br></div></td>';
                }
				else if (!isset($field->label) || $field->label == "")
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

					if (StringHelper::strlen($class) > 0)
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
            </li>
			<?php
		}

		if ($haslayouts)
		{
			?>
            <li>
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
						<li <?php echo $class; ?>><a data-toggle="tab"
						                             href="#<?php echo $viewfile; ?>"><?php echo $viewfile; ?></a></li>
						<?php
					}
				}
				?>
			</ul>
			<?php

			// Now get layout specific parameters
			//Form::addFormPath(JPATH_COMPONENT ."/views/");
			foreach (JEV_CommonFunctions::getJEventsViewList() as $viewfile)
			{

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

					$fieldSets = $layoutform->getFieldsets();
					$html      = array();
					$hasconfig = false;
					foreach ($fieldSets as $name => $fieldSet)
					{
						$html[] = '<div class="paramlist admintable form-horizontal" >';

						if (isset($fieldSet->description) && !empty($fieldSet->description))
						{
							$desc   = Text::_($fieldSet->description);
							$html[] = '<div class="paramlist_description" colspan="2">' . $desc . '</div>';
						}

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

							// Needed to deal with early execution of initTemplate in backend
							$fieldhtml = str_replace('btn-group', 'btn-group-ysts',$fieldhtml );

							$html[] = $fieldhtml;

							/*
$class = isset($field->class) ? $field->class : "";

if (StringHelper::strlen($class) > 0)
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

						echo implode("\n", $html);

					}
				}
			}
		}

		if ($hasPlugins)
		{
            ?>
            <li>
                <ul gsl-accordion class="gsl-list-divider">
            <?php
			$i = 0;
			foreach ($jevplugins as $plugin)
			{
				$config = JPATH_SITE . "/plugins/" . $plugin->type . "/" . $plugin->name . "/" . $plugin->name . ".xml";
				if (file_exists($config))
				{
					?>
					<li clas="gsl-card gsl-card-default gsl-card-hover">
					<?php
					// Load language file
					$lang     = Factory::getLanguage();
					$langfile = "plg_" . $plugin->type . "_" . $plugin->name . ".sys";
					$lang->load($langfile, JPATH_ADMINISTRATOR, null, false, true);
					$langfile = "plg_" . $plugin->type . "_" . $plugin->name;
					$lang->load($langfile, JPATH_ADMINISTRATOR, null, false, true);

					// Now get plugin specific parameters
					$pluginform = Form::getInstance("com_jevents.config.plugins." . $plugin->name, $config, array('control' => 'jform_plugin[' . $plugin->type . '][' . $plugin->name . ']', 'load_data' => true), true, "/extension/config/fields");
					$pluginparams = new JevRegistry($plugin->params);

					// Load the whole XML config file to get the plugin name in plain english
					$xml = new SimpleXMLElement($config, 0, true);
					// TODO Consider adding enabled/disabled method here for plugins inclusing unpublished ones!
					// TODO handle unpublished plugins too

					$hasfields = false;
					$fieldSets = $pluginform->getFieldsets();
					foreach ($fieldSets as $name => $fieldSet)
					{
						if ($pluginform->getFieldset($name))
						{
							$hasfields = true;
						}
					}
					$safedesc = Text::_($xml->description, true);
					$safename = Text::_($xml->name, true);

					// offer drop down IFF has fields!
					if ($hasfields)
					{
						$label = '<i class="icon-chevron-right"></i> ' . Text::_($xml->name);
					}
					else
					{
						$label = '<i class="icon-blank"></i> ' . Text::_($xml->name);
					}
					if ($safedesc)
					{
						$label .= '<i class="icon-info-sign icon-info" data-content="<strong>' . $safename . "</strong><br/>" . $safedesc . '" style="margin-left:10px;font-size:1.2em;"></i> ';
					}
					else
					{
						$label .= '<i class="icon-blank" style="margin-left:10px"></i> ';
					}

					$checked1 = $plugin->enabled ? 'checked="checked" ' : '';
					$checked0 = !$plugin->enabled ? 'checked="checked" ' : '';
					$label    .= '<fieldset class="btn-group radio"  style="float:right;">'
						. '<input type="radio"  ' . $checked1 . '  value="1" name="jform_plugin[' . $plugin->type . '][' . $plugin->name . '][enabled]"  id="jform_plugin_' . $plugin->type . '_' . $plugin->name . '_params_enabled1" class="btn">'
						. '<label for="jform_plugin_' . $plugin->type . '_' . $plugin->name . '_params_enabled1" class="btn">'
						. Text::_('JENABLED')
						. '</label>'
						. '<input type="radio" ' . $checked0 . ' value="0" name="jform_plugin[' . $plugin->type . '][' . $plugin->name . '][enabled]"  id="jform_plugin_' . $plugin->type . '_' . $plugin->name . '_params_enabled0" class="btn">'
						. '<label for="jform_plugin_' . $plugin->type . '_' . $plugin->name . '_params_enabled0" class="btn">'
						. Text::_('JDISABLED')
						. '</label>'
						. '</div>'
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
								$html[]    = $field->renderField();
							}
							$html[] = '</div>';
						}

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

						?>
                        <a class="gsl-accordion-title <?php echo $popclass;?>" href="#" <?php echo $labelinfo;?>  ><?php echo $label; ?></a>
                        <div class="gsl-accordion-content">
							<?php
							echo implode("\n", $html);
							?>
                        </div>
						<?php
					}
					else
					{
						if ($safedesc)
						{
							$popclass = " hasYsPopover";
							$labelinfo = '  data-yspoptitle="' . $safename . '" data-yspopcontent="' . $safedesc . '"  ';
							$labelinfo .= ' data-yspopoptions=\'{"mode" : "click, hover", "offset" : 20,"delayHide" : 200, "pos" : "top-left"}\' ';
						}
						else {
							$popclass = "";
							$labelinfo = '';
						}

						?>
					<a class="gsl-accordion-title <?php echo $popclass;?>" href="#" <?php echo $labelinfo;?> ><?php echo $label; ?></a>
					<div class="gsl-accordion-content">
						<?php echo JText::_("COM_JEVENTS_NO_CONFIG_OPTIONS");?>
					</div>
						<?php
					}
					?>
					</li>
					<?php
				}
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


