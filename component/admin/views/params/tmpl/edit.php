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
			?>
		</ul>

		<?php
		echo JHtml::_('bootstrap.startPane', 'myParamsTabs', array('active' => 'JEV_TAB_COMPONENT'));
		$fieldSets = $this->form->getFieldsets();
		$used = -2;
		foreach ($fieldSets as $name => $fieldSet)
		{
			if ($name == "permissions")
			{
				continue;
			}
			$label = empty($fieldSet->label) ? $name : $fieldSet->label;
			echo JHtml::_('bootstrap.addPanel', "myParamsTabs", $name);

			$html = array();
			$used++;
			if ($used == 0)
			{
				?>					
				<ul id="myTab" class="nav nav-tabs">
					<li class="active"><a href="#home" data-toggle="tab">Home</a></li>
					<li><a href="#profile" data-toggle="tab">Profile</a></li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="#dropdown1" data-toggle="tab">@fat</a></li>
							<li><a href="#dropdown2" data-toggle="tab">@mdo</a></li>
						</ul>
					</li>
				</ul>
				<div id="myTabContent" class="tab-content">
					<div class="tab-pane fade in active" id="home">
						<p>Raw denim you probably haven heard of them jean shorts Austin. Nesciunt tofu stumptown aliqua, retro synth master cleanse. Mustache cliche tempor, williamsburg carles vegan helvetica. Reprehenderit butcher retro keffiyeh dreamcatcher synth. Cosby sweater eu banh mi, qui irure terry richardson ex squid. Aliquip placeat salvia cillum iphone. Seitan aliquip quis cardigan american apparel, butcher voluptate nisi qui.</p>
					</div>
					<div class="tab-pane fade" id="profile">
						<p>Food truck fixie locavore, accusamus mcsweeneys marfa nulla single-origin coffee squid. Exercitation +1 labore velit, blog sartorial PBR leggings next level wes anderson artisan four loko farm-to-table craft beer twee. Qui photo booth letterpress, commodo enim craft beer mlkshk aliquip jean shorts ullamco ad vinyl cillum PBR. Homo nostrud organic, assumenda labore aesthetic magna delectus mollit. Keytar helvetica VHS salvia yr, vero magna velit sapiente labore stumptown. Vegan fanny pack odio cillum wes anderson 8-bit, sustainable jean shorts beard ut DIY ethical culpa terry richardson biodiesel. Art party scenester stumptown, tumblr butcher vero sint qui sapiente accusamus tattooed echo park.</p>
					</div>
					<div class="tab-pane fade" id="dropdown1">
						<p>Etsy mixtape wayfarers, ethical wes anderson tofu before they sold out mcsweeneys organic lomo retro fanny pack lo-fi farm-to-table readymade. Messenger bag gentrify pitchfork tattooed craft beer, iphone skateboard locavore carles etsy salvia banksy hoodie helvetica. DIY synth PBR banksy irony. Leggings gentrify squid 8-bit cred pitchfork. Williamsburg banh mi whatever gluten-free, carles pitchfork biodiesel fixie etsy retro mlkshk vice blog. Scenester cred you probably havent heard of them, vinyl craft beer blog stumptown. Pitchfork sustainable tofu synth chambray yr.</p>
					</div>
					<div class="tab-pane fade" id="dropdown2">
						<p>Trust fund seitan letterpress, keytar raw denim keffiyeh etsy art party before they sold out master cleanse gluten-free squid scenester freegan cosby sweater. Fanny pack portland seitan DIY, art party locavore wolf cliche high life echo park Austin. Cred vinyl keffiyeh DIY salvia PBR, banh mi before they sold out farm-to-table VHS viral locavore cosby sweater. Lomo wolf viral, mustache readymade thundercats keffiyeh craft beer marfa ethical. Wolf salvia freegan, sartorial keffiyeh echo park vegan.</p>
					</div>
				</div>
				<?php
			}
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
			/*
			  echo JHtml::_('bootstrap.addPanel', "myParamsTabs", "club_layouts");
			  ?>
			  <ul class="nav nav-tabs" id="myLayoutTabs">
			  <?php
			  $first = true;
			  foreach (JEV_CommonFunctions::getJEventsViewList() as $viewfile)
			  {
			  $config = JPATH_SITE . "/components/" . JEV_COM_COMPONENT . "/views/" . $viewfile . "/config.xml";
			  if (file_exists($config))
			  {

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
			  <li <?php echo $class; ?>><a data-toggle="tab" href="<?php echo $viewfile; ?>"><?php echo $viewfile; ?></a></li>
			  <?php
			  }
			  }
			  ?>
			  </ul>
			  <?php
			  echo JHtml::_('bootstrap.startPane', "myLayoutTabs");
			 */
			/*
			  foreach (JEV_CommonFunctions::getJEventsViewList() as $viewfile)
			  {
			  $config = JPATH_SITE . "/components/" . JEV_COM_COMPONENT . "/views/" . $viewfile . "/config.xml";
			  if (file_exists($config))
			  {
			  $activeviewfile = $viewfile;
			  break;
			  }
			  }

			  echo JHtml::_('bootstrap.startAccordion', 'myLayoutsliders', array('active' => $activeviewfile));
			 */
			// Now get layout specific parameters
			//JForm::addFormPath(JPATH_COMPONENT ."/views/");
			foreach (JEV_CommonFunctions::getJEventsViewList() as $viewfile)
			{

				$config = JPATH_SITE . "/components/" . JEV_COM_COMPONENT . "/views/" . $viewfile . "/config.xml";
				if (file_exists($config))
				{
					//echo JHtml::_('bootstrap.addSlide', 'myLayoutsliders', ucfirst($viewfile), $viewfile);
					//echo JHtml::_('bootstrap.addPanel', 'myLayoutTabs', $viewfile);
					echo JHtml::_('bootstrap.addPanel', 'myParamsTabs', $viewfile);

					$layoutform = JForm::getInstance("com_jevent.config.layouts." . $viewfile, $config, array('control' => 'jform', 'load_data' => true), true, "/config");
					$layoutform->bind($this->component->params);

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

					//echo JHtml::_('bootstrap.endSlide');
					//echo JHtml::_('bootstrap.endPanel');
					echo JHtml::_('bootstrap.endPanel');
				}
			}
			//echo JHtml::_('bootstrap.endAccordion');
			//echo JHtml::_('bootstrap.endPane', 'myLayoutTabs');
			//echo JHtml::_('bootstrap.endPanel');
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



