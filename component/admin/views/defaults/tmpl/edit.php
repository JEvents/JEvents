<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: edit.php 2768 2011-10-14 08:43:42Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2017 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.filesystem.file' );

if ($this->item->name == "month.calendar_cell" || $this->item->name == "month.calendar_tip" || $this->item->name == "icalevent.edit_page")
{
	$editor =  JEditor::getInstance("none");
}
else
{
	$editor = JFactory::getConfig()->get('editor');
	$editor =  JEditor::getInstance($editor);
}

if (strpos($this->item->name, "com_") === 0)
{
	$lang = JFactory::getLanguage();
	$parts = explode(".", $this->item->name);
	$lang->load($parts[0]);
}


if (JevJoomlaVersion::isCompatible("3.0.0"))
{
	if ($this->item->value == "" && file_exists(dirname(__FILE__) . '/' . $this->item->name . ".3.html"))
		$this->item->value = file_get_contents(dirname(__FILE__) . '/' . $this->item->name . ".3.html");
}
if ($this->item->value == "" && file_exists(dirname(__FILE__) . '/' . $this->item->name . ".html"))
	$this->item->value = file_get_contents(dirname(__FILE__) . '/' . $this->item->name . ".html");

//Float layout check to load default value
if ($this->item->name == 'icalevent.list_block1' && $this->item->value == "" && Jfile::exists(JPATH_SITE . '/components/com_jevents/views/float/defaults/icalevent.list_block1.html')) {
	$this->item->value = file_get_contents(JPATH_SITE . '/components/com_jevents/views/float/defaults/icalevent.list_block1.html');
}
if ($this->item->name == 'icalevent.list_block2' && $this->item->value == "" && Jfile::exists(JPATH_SITE . '/components/com_jevents/views/float/defaults/icalevent.list_block2.html')) {
	$this->item->value = file_get_contents(JPATH_SITE . '/components/com_jevents/views/float/defaults/icalevent.list_block2.html');
}

$this->replaceLabels($this->item->value);
?>		
<div id="jevents">
	<form action="index.php" method="post" name="adminForm" id="adminForm" >
		<table width="90%" border="0" cellpadding="2" cellspacing="2" class="adminform" >
			<tr>
				<td>
					<input type="hidden" name="name" value="<?php echo $this->item->name; ?>">
					<input type="hidden" name="id" value="<?php echo $this->item->id; ?>">
					<input type="hidden" name="language" value="<?php echo $this->item->language; ?>">
					<input type="hidden" name="catid" value="<?php echo $this->item->catid; ?>">

					<script type="text/javascript" >
						<!--//
						Joomla.submitbutton = function(pressbutton) {
							var form = document.adminForm;
<?php
// in case editor is toggled off - needed for TinyMCE
echo $editor->save('value');
?>
							submitform(pressbutton);
						}
//-->
					</script>
					<div class="adminform" align="left">
						<div style="margin-bottom:20px;">
							<table cellpadding="5" cellspacing="0" border="0" >
								<tr>
									<td align="left"><?php echo JText::_('TITLE'); ?>:</td>
									<td colspan="2">
										<?php echo htmlspecialchars(JText::_($this->item->title), ENT_QUOTES, 'UTF-8'); ?>
										<!--<input class="inputbox" type="text" name="title" size="50" maxlength="100" value="<?php echo htmlspecialchars($this->item->title, ENT_QUOTES, 'UTF-8'); ?>" />//-->
									</td>
								</tr>
								<tr>
									<td align="left"><?php echo JText::_('JFIELD_LANGUAGE_LABEL'); ?>:</td>
									<td colspan="2">
										<?php echo $this->item->language == "*" ? JText::alt('JALL', 'language') : $this->item->language; ?>
									</td>
								</tr>
								<tr>
									<td align="left"><?php echo JText::_('JCATEGORY'); ?>:</td>
									<td colspan="2">
										<?php echo $this->item->catid == "0" ? JText::alt('JALL', 'language') : $this->item->category_title; ?>
									</td>
								</tr>
								<tr>
									<td align="left"><?php echo JText::_('NAME'); ?>:</td>
									<td colspan="2">
										<?php echo htmlspecialchars($this->item->name, ENT_QUOTES, 'UTF-8'); ?>
									</td>
								</tr>
								<tr class="jevpublished">
									<td><?php echo JText::_("JSTATUS"); ?></td>
									<td colspan="3">
									<?php
									$poptions = array();
									$poptions[] = JHTML::_('select.option', 0, JText::_("JUNPUBLISHED"));
									$poptions[] = JHTML::_('select.option', 1, JText::_("JPUBLISHED"));
                                                                        $poptions[] = JHTML::_('select.option', -1, JText::_("JTRASHED"));
									echo JHTML::_('select.genericlist', $poptions, 'state', 'class="inputbox" size="1"', 'value', 'text', $this->item->state);
									?>
									</td>
								</tr>

								<tr class="layouteditor">
									<td valign="top" align="left">
										<?php echo JText::_('JEV_LAYOUT'); ?>
									</td>
									<td >
										<?php
// parameters : areaname, content, hidden field, width, height, rows, cols
										echo $editor->display('value', htmlspecialchars($this->item->value, ENT_QUOTES, 'UTF-8'), 700, 450, '70', '15', false);
										?>
									</td>
									<td valign="top">
										<?php
										$pattern = "#.*([0-9]*).*#";
										$name = preg_replace("#\.[0-9]+#", "", $this->item->name);
										$selectbox = $this->loadTemplate($name);
										echo $selectbox;
										?>
									</td>
								</tr>
							</table>
						</div>
					</div>

					<?php
					if ($this->item->name != "month.calendar_tip" && $this->item->name != "icalevent.edit_page" && strpos($this->item->name, "com_jevpeople")===false && strpos($this->item->name, "com_jevlocations")===false)
					{
						?>
					<h3><?php echo JText::_("JEV_DEFAULTS_CUSTOM_MODULES");?></h3>
					<?php

					$params = new JRegistry($this->item->params);
					$modids = $params->get("modid", array());
					$modvals = $params->get("modval", array());
					// not sure how this can arise :(
					if (is_object($modvals)){
						$modvals = get_object_vars($modvals);
					}
					$modids = array_values($modids);
					$modvals = array_values($modvals);

					$count = 0;
					$conf = JFactory::getConfig();
					$modeditor =  $editor;

					foreach ($modids as $modid)
					{
						if (trim($modid)=="") {
							$count ++;
							continue;
						}
						?>
						<table cellpadding="5" cellspacing="0" border="0" >
							<tr>
								<td align="left" ><?php echo JText::_('JEV_DEFAULTS_MODULE_ID'); ?>:</td>
								<td align="left" colspan="2"><input name="params[modid][]" id="modid<?php echo $count;?>" type="text" size="40" value="<?php echo $modid?>" /></td>
							</tr>
							<tr>
								<td align="left" ><?php echo JText::_('JEV_DEFAULTS_MODULE_OUTPUT'); ?>:</td>
								<td align="left"><?php echo $modeditor->display('params[modval]['.$count."]", htmlspecialchars($modvals[$count], ENT_QUOTES, 'UTF-8'), 700, 450, '70', '15', false,'modval'.$count );?></td>
								<td align="left" valign="top"><?php echo str_replace("value", "modval".$count, str_replace("jevdefaults", "jevmods".$count, $selectbox));?></td>
							</tr>
						</table>
						<?php
						$count ++;
					}
					// plus one extra one
					?>
					<table cellpadding="5" cellspacing="0" border="0" >
						<tr>
							<td align="left" ><?php echo JText::_('JEV_DEFAULTS_MODULE_ID'); ?>:</td>
							<td align="left" colspan="2"><input name="params[modid][]" id="modid<?php echo $count;?>" type="text" size="40" /></td>
						</tr>
						<tr>
							<td align="left" ><?php echo JText::_('JEV_DEFAULTS_MODULE_OUTPUT'); ?>:</td>
							<td align="left"><?php echo $modeditor->display('params[modval]['.$count."]", htmlspecialchars("", ENT_QUOTES, 'UTF-8'), 700, 450, '70', '15', false,'modval'.$count );?></td>
							<td align="left" valign="top"><?php echo str_replace("value", "modval".$count, str_replace("jevdefaults", "jevmods".$count, $selectbox));?></td>
						</tr>
					</table>
					<?php
					}
					?>
				</td>
			</tr>
		</table>
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="task" value="defaults.edit" />
		<input type="hidden" name="act" value="" />
		<input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT; ?>" />
	</form>
</div>