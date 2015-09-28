<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: edit16.php 2983 2011-11-10 14:02:23Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2015 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');
$version = JEventsVersion::getInstance();
?>
<form action="index.php" method="post" name="adminForm" autocomplete="off" id="adminForm">

	<fieldset class='jevconfig'>
		<legend>
			<?php echo JText::_('JEV_EVENTS_CONFIG'); ?>
		</legend>
		<div style="float:right;margin-top:-20px;background-color:#ffffff;padding:2px;">
			[<?php echo $version->getShortVersion(); ?>&nbsp;<a href='<?php echo $version->getURL(); ?>'><?php echo JText::_('JEV_CHECK_VERSION'); ?> </a>]
		</div>

		<?php
		$haslayouts = false;
		foreach (JEV_CommonFunctions::getJEventsViewList() as $viewfile)
		{
			$config = JPATH_SITE . "/components/" . JEV_COM_COMPONENT . "/views/" . $viewfile . "/config.xml";
			if (file_exists($config))
			{
				$haslayouts = true;
			}
		}

		echo JHtml::_('tabs.start', 'config-tabs-' . $this->component->option . '_configuration', array('useCookie' => 1));
		$fieldSets = $this->form->getFieldsets();
		foreach ($fieldSets as $name => $fieldSet)
		{
			if ($name == "permissions")
			{
				continue;
			}
			$label = empty($fieldSet->label) ? $name : $fieldSet->label;
			echo JHtml::_('tabs.panel', JText::_($label), 'publishing-details');

			$html = array();
			$html[] = '<table width="100%" class="paramlist admintable" cellspacing="1">';

			if (isset($fieldSet->description) && !empty($fieldSet->description))
			{
				$desc = JText::_($fieldSet->description);
				$html[] = '<tr><td class="paramlist_description" colspan="2">' . $desc . '</td></tr>';
			}

			foreach ($this->form->getFieldset($name) as $field)
			{
				// do not show difficulty option in Joomla 2.5
				if ($field->hidden || $field->fieldname=="com_difficulty")
				{
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

				$maxjoomlaversion = $this->form->getFieldAttribute($field->fieldname, "maxjoomlaversion", false);
				if ( $maxjoomlaversion && version_compare(JVERSION,$maxjoomlaversion , ">")) {
					continue;
				}
				$minjoomlaversion = $this->form->getFieldAttribute($field->fieldname, "minjoomlaversion", false);
				if ( $minjoomlaversion && version_compare(JVERSION,$minjoomlaversion , "<")) {
					continue;
				}

				$class = isset($field->class) ? $field->class : "";

				if (JString::strlen($class) > 0)
				{
					$class = " class='$class'";
				}
				$html[] = "<tr $class>";
				if (!isset($field->label) || $field->label == "")
				{
					$html[] = '<td width="40%" class="paramlist_key"><span class="editlinktip">' . $field->label . '</span></td>';
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

			<div class="clr"></div>
			<?php
		}

		if ($haslayouts)
		{
			echo JHtml::_('tabs.panel', JText::_("CLUB_LAYOUTS"), "CLUB_LAYOUTS");
			echo JHtml::_('tabs.start', 'layouts');
		}
		// Now get layout specific parameters
		//JForm::addFormPath(JPATH_COMPONENT ."/views/");
		foreach (JEV_CommonFunctions::getJEventsViewList() as $viewfile)
		{
			$config = JPATH_SITE . "/components/" . JEV_COM_COMPONENT . "/views/" . $viewfile . "/config.xml";
			if (file_exists($config))
			{
				$layoutform = JForm::getInstance("com_jevent.config.layouts.".$viewfile, $config, array('control'=>'jform', 'load_data'=>true), true,"/config");
				$layoutform->bind($this->component->params);

				if (JFile::exists(JPATH_ADMINISTRATOR."/manifests/files/$viewfile.xml")){
					$xml = simplexml_load_file(JPATH_ADMINISTRATOR."/manifests/files/$viewfile.xml");
					$layoutname = (string) $xml->name;
					$langfile = 'files_' . str_replace('files_', '', strtolower(JFilterInput::getInstance()->clean((string) $layoutname, 'cmd')));
					$lang = JFactory::getLanguage();
					 $lang->load($langfile , JPATH_SITE, null, false, true);
				}

				echo JHtml::_('tabs.panel', JText::_(ucfirst($viewfile)), 'config_' . str_replace(" ", "_", $viewfile));
				
				$fieldSets = $layoutform->getFieldsets();
				foreach ($fieldSets as $name => $fieldSet)
				{
					$html = array();
					$html[] = '<table width="100%" class="paramlist admintable" cellspacing="1">';

					if (isset($fieldSet->description) && !empty($fieldSet->description))
					{
						$desc = JText::_($fieldSet->description);
						$html[] = '<tr><td class="paramlist_description" colspan="2">' . $desc . '</td></tr>';
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

						$class = isset($field->class) ? $field->class : "";

						if (JString::strlen($class) > 0)
						{
							$class = " class='$class'";
						}
						$html[] = "<tr $class>";
						if (!isset($field->label) || $field->label == "")
						{
							$html[] = '<td width="40%" class="paramlist_key"><span class="editlinktip">' . $field->label . '</span></td>';
							$html[] = '<td class="paramlist_value">' . $field->input . '</td>';
						}
						else
						{
							$html[] = '<td class="paramlist_value" colspan="2">' . $field->input . '</td>';
						}

						$html[] = '</tr>';
					}
				}
				$html[] = '</table>';

				echo implode("\n", $html);
				
			}
		}
		if ($haslayouts)
		{
			echo JHtml::_('tabs.end');
		}

		echo JHtml::_('tabs.end');
		?>

		<div class="clr"></div>

	</fieldset>

	<input type="hidden" name="id" value="<?php echo $this->component->id; ?>" />
	<input type="hidden" name="component" value="<?php echo $this->component->option; ?>" />

	<input type="hidden" name="controller" value="component" />
	<input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT; ?>" />
	<input type="hidden" name="task" value="" />
<?php echo JHTML::_('form.token'); ?>
</form>