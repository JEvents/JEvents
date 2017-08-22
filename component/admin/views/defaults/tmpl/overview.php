<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
JHtml::_('behavior.core');
JHtml::_('bootstrap.tooltip');

$pathIMG = JURI::root() . '/administrator/images/';
$mainspan = 10;
 $fullspan = 12;

?>
<?php if (!empty($this->sidebar)) : ?>
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
 <?php endif; ?>

<form action="index.php" method="post"  name="adminForm" id="adminForm">		
		<div id="j-main-container" class="span<?php echo (!empty($this->sidebar)) ? $mainspan : $fullspan; ?>  ">
			<fieldset id="filter-bar">
				<div class="filter-select fltrt">
<?php 
/*
if (count($this->languages) > 1)
{ ?>
						<select name="filter_language" class="inputbox" onchange="this.form.submit()">
							<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE'); ?></option>
						<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->language); ?>
						</select>
						<?php
} 
 */
?>
<?php if ($this->catids)
{ ?>
						<select name="filter_catid" class="inputbox" onchange="this.form.submit()">
							<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY'); ?></option>
<?php echo $this->catids; ?>
						</select>
						<?php } ?>
					<select name="filter_layout_type" class="inputbox" onchange="this.form.submit()">
<?php echo $this->addonoptions; ?>
					</select>
					<select name="filter_published" class="inputbox" onchange="this.form.submit()">
						<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED'); ?></option>
<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array("trash" => 0, "archived" => 0, "all" => 0)), 'value', 'text', $this->filter_published, true); ?>
					</select>			
				</div>
			</fieldset>


			<div id="editcell">
				<table class="adminlist   table table-striped">
					<thead>
						<tr>
							<th width="20" nowrap="nowrap">
								<?php echo JHtml::_('grid.checkall'); ?>
							</th>
							<th width="5">
								<?php echo JText::_('NUM'); ?>
							</th>
							<th class="title">
								<?php echo JText::_('TITLE'); ?>
							</th>
							<th class="title">
							<?php echo JText::_('NAME'); ?>
							</th>
								<?php 
								if (count($this->languages) > 1)
								{ ?>
								<th >
	<?php echo JText::_('JGRID_HEADING_LANGUAGE'); ?>
								</th>
								<?php
								  } 
								 if ($this->catids)
								{ ?>
								<th >
	<?php echo JText::_('JCATEGORY'); ?>
								</th>
<?php } ?>
							<th width="10%" nowrap="nowrap"><?php echo JText::_('JEV_PUBLISHED'); ?></th>			
						</tr>
					</thead>
					<tbody>
						<?php
						$k = 0;
						for ($i = 0, $n = count($this->items); $i < $n; $i++)
						{
							$row = &$this->items[$i];

							if (strpos($row->name, "com_") === 0)
							{
								$lang = JFactory::getLanguage();
								$parts = explode(".", $row->name);
								$lang->load($parts[0]);
							}
							$link = JRoute::_('index.php?option=' . JEV_COM_COMPONENT . '&task=defaults.edit&id=' . $row->id);
							?>
							<tr class="<?php echo "row$k"; ?>">
								<td width="20" >
									<?php echo JHtml::_('grid.id', $i, $row->id); ?>
								</td>
								<td>
	<?php echo $i + 1; ?>
								</td>
								<td>
									<span class="editlinktip hasTip" title="<?php echo JText::_('JEV_Edit_Layout'); ?>::<?php echo $this->escape(JText::_($row->title)); ?>">
										<a href="<?php echo $link; ?>">
									<?php echo $this->escape(JText::_($row->title)); ?></a>
									</span>
								</td>
								<td>
								<?php echo $this->escape($row->name); ?>

								</td>
									<?php
									if (count($this->languages) > 1)
									{ ?>
									<td class="center">
										<?php echo $this->translationLinks($row);
										/*
										if ($row->language == '*'): 
											 echo JText::alt('JALL', 'language');
										else:
											echo $row->language_title ? $this->escape($row->language_title) : JText::_('JUNDEFINED'); 
										endif; 
										 */
										?>
									</td>
									<?php } ?>
								<?php if ($this->catids)
								{ ?>
									<td class="center">
										<?php if ($row->catid == '0'): ?>
										<?php echo JText::alt('JALL', 'language'); ?>
									<?php else: ?>
											<?php echo $row->category_title ? $this->escape($row->category_title) : JText::_('JUNDEFINED'); ?>
										<?php endif; ?>
									</td>
								<?php } ?>

								<td align="center">
	<?php
	$img = $row->state ? JHTML::_('image', 'admin/tick.png', '', array('title' => ''), true) : JHTML::_('image', 'admin/publish_x.png', '', array('title' => ''), true);
	?>
									<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo $row->state ? 'defaults.unpublish' : 'defaults.publish'; ?>')"><?php echo $img; ?></a>
								</td>
							</tr>
	<?php
	$k = 1 - $k;
}
?>
					</tbody>
				</table>
			</div>

			<input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT; ?>" />
			<input type="hidden" name="task" value="defaults.list" />
			<input type="hidden" name="boxchecked" value="0" />
<?php echo JHTML::_('form.token'); ?>
		</div>
</form>
