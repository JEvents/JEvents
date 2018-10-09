<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: edit.php 2768 2011-10-14 08:43:42Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2018 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('formbehavior.chosen', 'select');
jimport('joomla.filesystem.file');

if ($this->item->name == "month.calendar_cell" || $this->item->name == "month.calendar_tip" || $this->item->name == "icalevent.edit_page")
{
	$editor = JEditor::getInstance("none");
}
else
{
	$editor = Factory::getConfig()->get('editor');
	$editor = JEditor::getInstance($editor);
}

if (strpos($this->item->name, "com_") === 0)
{
	$lang  = Factory::getLanguage();
	$parts = explode(".", $this->item->name);
	$lang->load($parts[0]);
}

if ($this->item->value == "" && file_exists(dirname(__FILE__) . '/' . $this->item->name . ".3.html"))
	$this->item->value = file_get_contents(dirname(__FILE__) . '/' . $this->item->name . ".3.html");

if ($this->item->value == "" && file_exists(dirname(__FILE__) . '/' . $this->item->name . ".html"))
	$this->item->value = file_get_contents(dirname(__FILE__) . '/' . $this->item->name . ".html");

//Float layout check to load default value
if ($this->item->name == 'icalevent.list_block1' && $this->item->value == "" && Jfile::exists(JPATH_SITE . '/components/com_jevents/views/float/defaults/icalevent.list_block1.html'))
{
	$this->item->value = file_get_contents(JPATH_SITE . '/components/com_jevents/views/float/defaults/icalevent.list_block1.html');
}
if ($this->item->name == 'icalevent.list_block2' && $this->item->value == "" && Jfile::exists(JPATH_SITE . '/components/com_jevents/views/float/defaults/icalevent.list_block2.html'))
{
	$this->item->value = file_get_contents(JPATH_SITE . '/components/com_jevents/views/float/defaults/icalevent.list_block2.html');
}

$this->replaceLabels($this->item->value);
?>
<div id="jevents">
	<form action="index.php" method="post" name="adminForm" id="adminForm" class="customlayouts">
		<div class="container-fluid">
			<div class="row">
				<div class="form-group span3">
					<label for="title"><?php echo JText::_('TITLE'); ?>:</label>
					<input readonly class="inputbox form-control" type="text" id="title" size="50"
					       maxlength="100"
					       value="<?php echo htmlspecialchars(JText::_($this->item->title), ENT_QUOTES, 'UTF-8'); ?>"/>
				</div>
				<div class="form-group span3">
					<label for="language"><?php echo JText::_('JFIELD_LANGUAGE_LABEL'); ?>:</label>
					<input readonly class="inputbox form-control" type="text" id="language" size="50"
					       maxlength="100"
					       value="<?php echo $this->item->language == "*" ? JText::alt('JALL', 'language') : $this->item->language; ?>"/>
				</div>
				<div class="form-group span3">
					<label for="category"><?php echo JText::_('JCATEGORY'); ?>:</label>
					<input readonly class="inputbox form-control" type="text" id="language" size="50"
					       maxlength="100"
					       value="<?php echo $this->item->catid == "0" ? JText::alt('JALL', 'language') : $this->item->category_title; ?>"/>
				</div>
				<div class="form-group span3">
					<label for="name"><?php echo JText::_('NAME'); ?></label>
					<input readonly class="inputbox form-control" type="text" id="name" size="50"
					       maxlength="100"
					       value="<?php echo htmlspecialchars($this->item->name, ENT_QUOTES, 'UTF-8'); ?>"/>
				</div>
			</div>
			<div class="row">
				<div class="form-group jevpublished span3">
					<label for="published"><?php echo JText::_("JSTATUS"); ?></label>
					<?php
					$poptions   = array();
					$poptions[] = HTMLHelper::_('select.option', 0, JText::_("JUNPUBLISHED"));
					$poptions[] = HTMLHelper::_('select.option', 1, JText::_("JPUBLISHED"));
					$poptions[] = HTMLHelper::_('select.option', -1, JText::_("JTRASHED"));
					echo HTMLHelper::_('select.genericlist', $poptions, 'state', 'class="inputbox form-control chzn-color-state"', 'value', 'text', $this->item->state);
					?>
				</div>
				<div class="form-group span9">
					<?php
					$pattern   = "#.*([0-9]*).*#";
					$name      = preg_replace("#\.[0-9]+#", "", $this->item->name);
					$selectbox = $this->loadTemplate($name);
					echo $selectbox;
					?>
				</div>
			</div>
			<div class="row">
				<div class="form-group span12">
					<br>
					<label for="value"> <?php echo JText::_('JEV_LAYOUT'); ?></label>
				</div>
			</div>
			<div class="row">
				<div class="layouteditor">
					<?php
					// parameters : areaname, content, hidden field, width, height, rows, cols
					echo $editor->display('value', htmlspecialchars($this->item->value, ENT_QUOTES, 'UTF-8'), 700, 450, '70', '15', false);
					?>
				</div>
			</div>
		</div>

		<!-- Custom Module Form -->
		<?php
		if ($this->item->name != "month.calendar_tip" && $this->item->name != "icalevent.edit_page" && strpos($this->item->name, "com_jevpeople") === false && strpos($this->item->name, "com_jevlocations") === false)
		{
			?>
			<div class="container-fluid">

				<div class="row">
					<h3><?php echo JText::_("JEV_DEFAULTS_CUSTOM_MODULES"); ?></h3>
				</div>
				<?php
				$params  = new JevRegistry($this->item->params);
				$modids  = $params->get("modid", array());
				$modvals = $params->get("modval", array());

				// Not sure how this can arise :(
				if (is_object($modvals))
				{
					$modvals = get_object_vars($modvals);
				}
				$modids  = array_values($modids);
				$modvals = array_values($modvals);

				$count     = 0;
				$conf      = JFactory::getConfig();
				$modeditor = $editor;

				foreach ($modids as $modid)
				{
					if (trim($modid) == "")
					{
						$count++;
						continue;
					}
					?>
					<div class="row">
						<div class="form-group span3">
							<label for="title"><?php echo JText::_('JEV_DEFAULTS_MODULE_ID'); ?>:</label>
							<input class="inputbox form-control" type="text" id="modid<?php echo $count; ?>" size="50"
							       maxlength="100" name="params[modid][]" value="<?php echo $modid ?>"/>
						</div>
						<div class="form-group span9">
							<?php echo str_replace("value", "modval" . $count, str_replace("jevdefaults", "jevmods" . $count, $selectbox)); ?>
						</div>
					</div>
					<div class="row">
						<div class="form-group span12">
							<label for="title"><?php echo JText::_('JEV_DEFAULTS_MODULE_OUTPUT'); ?>:</label>
							<?php echo $modeditor->display('params[modval][' . $count . "]", htmlspecialchars($modvals[$count], ENT_QUOTES, 'UTF-8'), 700, 450, '70', '15', false, 'modval' . $count); ?>
						</div>
					</div>
					<div class="row">
						<hr/>
					</div>
					<?php
					$count++;
				}

				// Plus one extra one
				?>
				<div class="row">
					<div class="form-group span3">
						<label for="title"><?php echo JText::_('JEV_DEFAULTS_MODULE_ID'); ?>:</label>
						<input class="inputbox form-control" type="text" id="modid<?php echo $count; ?>" size="50"
						       maxlength="100" name="params[modid][]"/>
					</div>
					<div class="form-group span9">
						<?php echo str_replace("value", "modval" . $count, str_replace("jevdefaults", "jevmods" . $count, $selectbox)); ?>
					</div>
				</div>
				<div class="row">
					<div class="form-group span12">
						<label for="title"><?php echo JText::_('JEV_DEFAULTS_MODULE_OUTPUT'); ?>:</label>
						<?php echo $modeditor->display('params[modval][' . $count . "]", htmlspecialchars("", ENT_QUOTES, 'UTF-8'), 700, 450, '70', '15', false, 'modval' . $count); ?>
					</div>
				</div>
				<div class="row">
					<hr/>
				</div>
			</div>
		<?php } ?>
		<input type="hidden" name="name" value="<?php echo $this->item->name; ?>">
		<input type="hidden" name="id" value="<?php echo $this->item->id; ?>">
		<input type="hidden" name="language" value="<?php echo $this->item->language; ?>">
		<input type="hidden" name="catid" value="<?php echo $this->item->catid; ?>">
		<input type="hidden" name="boxchecked" value="0"/>
		<input type="hidden" name="task" value="defaults.edit"/>
		<input type="hidden" name="act" value=""/>
		<input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT; ?>"/>
	</form>
</div>