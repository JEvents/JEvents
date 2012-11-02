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

jimport('joomla.html.html.bootstrap');

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
?>

<form action="index.php" method="post" name="adminForm" autocomplete="off" id="adminForm">

	<fieldset class='jevconfig'>
		<legend>
			<?php echo JText::_('JEV_EVENTS_CONFIG'); ?>
		</legend>
		<div style="float:right;margin-top:-20px;background-color:#ffffff;padding:2px;">
			[<?php echo $version->getShortVersion(); ?>&nbsp;<a href='<?php echo $version->getURL(); ?>'><?php echo JText::_('JEV_CHECK_VERSION'); ?> </a>]
		</div>

		<ul class="nav nav-tabs" id="myParamsTabs">
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
				if ($first)
				{
					$first = false;
					$class = ' class="active"';
				}
				else
				{
					$class = '';
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

					$fieldSets = $layoutform->getFieldsets();
					$html = array();
					$hasconfig = false;
					foreach ($fieldSets as $name => $fieldSet)
					{
						$html[] = '<table class="paramlist admintable" >';

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
							$hasconfig = true;
							$class = isset($field->class) ? $field->class : "";

							if (strlen($class) > 0)
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
						}
						$html[] = '</table>';
					}

					if (!$hasconfig) {
						$x = 1;
					}
					if ($hasconfig) {
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
		echo JHtml::_('bootstrap.endPane', 'myParamsTabs');
		?>


	</fieldset>

	<input type="hidden" name="id" value="<?php echo $this->component->id; ?>" />
	<input type="hidden" name="component" value="<?php echo $this->component->option; ?>" />

	<input type="hidden" name="controller" value="component" />
	<input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_('form.token'); ?>
</form>



