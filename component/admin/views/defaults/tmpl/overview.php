<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php JHTML::_('behavior.tooltip'); 
$pathIMG = JURI::root().'/administrator/images/';
?>

<form action="index.php" method="post"  name="adminForm" id="adminForm">
<div id="editcell">
	<table class="adminlist">
	<thead>
		<tr>
			<th width="20" nowrap="nowrap">
		            <input type="checkbox" name="toggle" value="" onclick="<?php echo JVersion::isCompatible("3.0")?"Joomla.checkAll(this)":"checkAll(".count( $this->items ).")"; ?>" />
			</th>
			<th width="5">
				<?php echo JText::_( 'NUM' ); ?>
			</th>
			<th class="title">
				<?php echo JText::_( 'TITLE' ); ?>
			</th>
			<th class="title">
				<?php echo JText::_( 'NAME' ); ?>
			</th>
			<th width="10%" nowrap="nowrap"><?php echo JText::_('JEV_PUBLISHED'); ?></th>			
		</tr>
	</thead>
	<tbody>
	<?php
	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)
	{
		$row = &$this->items[$i];

		if (strpos($row->name, "com_")===0){
			$lang = JFactory::getLanguage();
			$parts = explode(".",$row->name);
			$lang->load($parts[0]);
		}
		$link 	= JRoute::_( 'index.php?option='.JEV_COM_COMPONENT.'&task=defaults.edit&name='. $row->name );

		?>
		<tr class="<?php echo "row$k"; ?>">
        	<td width="20" >
                <input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->name; ?>" onclick="isChecked(this.checked);" />
        	</td>
			<td>
				<?php echo $i+1; ?>
			</td>
			<td>
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'JEV_Edit_Layout' );?>::<?php echo $this->escape($row->title); ?>">
					<a href="<?php echo $link; ?>">
					<?php echo $this->escape(JText::_($row->title)); ?></a>
				</span>
			</td>
			<td>
				<?php echo $this->escape($row->name); ?>
			</td>
          	<td align="center">
          	<?php
		$img =  $row->state?JHTML::_('image','admin/tick.png', '',array('title'=>''),true):JHTML::_('image','admin/publish_x.png', '',array('title'=>''),true);
          	?>
          	<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo $row->state ? 'defaults.unpublish' : 'defaults.publish'; ?>')"><?php echo  $img; ?></a>
          	</td>
		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
	</tbody>
	</table>
</div>

	<input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT;?>" />
	<input type="hidden" name="task" value="defaults.list" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
