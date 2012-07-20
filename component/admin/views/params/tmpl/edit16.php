<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: edit16.php 2983 2011-11-10 14:02:23Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2009 GWE Systems Ltd
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
				if ($field->hidden)
				{
					continue;
				}
				$class = isset($field->class) ? $field->class : "";

				if (strlen($class) > 0)
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

					if (strlen($class) > 0)
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

		$haslayouts = false;
		foreach (JEV_CommonFunctions::getJEventsViewList() as $viewfile)
		{
			$config = JPATH_SITE . "/components/" . JEV_COM_COMPONENT . "/views/" . $viewfile . "/config.xml";
			if (file_exists($config))
			{
				$haslayouts = true;
			}
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
						$class = isset($field->class) ? $field->class : "";

						if (strlen($class) > 0)
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

		<?php
		/*
		  $names = array();
		  $groups = $this->params->getGroups();
		  if (count($groups)>0){
		  echo JHtml::_('tabs.start', 'configs');

		  $strings=array();
		  $tips=array();
		  foreach ($groups as $group=>$count) {
		  if ($group!="_default" && $count>0){
		  echo JHtml::_('tabs.panel', JText::_($group),  'config_'.str_replace(" ","_",$group));
		  echo $this->params->render('params',$group);

		  if ($group=="JEV_PERMISSIONS"){
		  $fieldSets = $this->form->getFieldsets();
		  foreach ($fieldSets as $name => $fieldSet) {
		  foreach ($this->form->getFieldset($name) as $field) {
		  echo $field->label;
		  echo $field->input;
		  }
		  }

		  }
		  }
		  }

		  $haslayouts = false;
		  foreach (JEV_CommonFunctions::getJEventsViewList() as $viewfile) {
		  $config = JPATH_SITE . "/components/".JEV_COM_COMPONENT."/views/".$viewfile."/config.xml";
		  if (file_exists($config)){
		  $haslayouts = true;
		  }
		  }

		  if ($haslayouts){
		  echo JHtml::_('tabs.panel', JText::_("CLUB_LAYOUTS"), "CLUB_LAYOUTS");
		  echo JHtml::_('tabs.start', 'layouts');
		  }
		  // Now get layout specific parameters
		  foreach (JEV_CommonFunctions::getJEventsViewList() as $viewfile) {
		  $config = JPATH_SITE . "/components/".JEV_COM_COMPONENT."/views/".$viewfile."/config.xml";
		  if (file_exists($config)){
		  $viewparams = new JevParameter( $this->params->toString(), $config );
		  echo JHtml::_('tabs.panel', JText::_(ucfirst($viewfile)), 'config_'.str_replace(" ","_",$viewfile));
		  echo $viewparams->render();
		  }
		  }
		  if ($haslayouts){
		  echo JHtml::_('tabs.end');
		  }
		  echo JHtml::_('tabs.end');
		  }
		  else {
		  echo $this->params->render();
		  }
		 */
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