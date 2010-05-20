<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: overview.php 1479 2009-06-25 14:40:55Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

global   $option;
$user =& JFactory::getUser();
$db =& JFactory::getDBO();

if( isset( $this->message) &&  $this->message != null ) {?>
<div class="message"><?php echo $this->message;?></div>
<?php
}
$url = JRoute::_("index.php?option=".$option);
?>
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
	<div id="jevuser">
	    <form action="<?php echo $url;?>" method="post" name="adminForm">
	<br/>
  <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
	<thead>
    <tr>
      <th width="20"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->users); ?>);" /></th>
      <th class="title" width="20%" align="left"  nowrap="nowrap"><?php echo JText::_('Name');?></th>
      <th width="20%" align="left" nowrap="nowrap"><?php echo JText::_('Username');?></th>
      <th align="center" nowrap="nowrap"><?php echo JText::_('Enabled?');?></th>
      <th align="center" nowrap="nowrap"><?php echo JText::_('Create?');?></th>
      <th align="center" nowrap="nowrap"><?php echo JText::_('Max Events?');?></th>
      <th align="center" nowrap="nowrap"><?php echo JText::_('Publish Own?');?></th>
      <th align="center" nowrap="nowrap"><?php echo JText::_('Delete Own?');?></th>
      <th align="center" nowrap="nowrap"><?php echo JText::_('Edit All?');?></th>
      <th align="center" nowrap="nowrap"><?php echo JText::_('Publish All?');?></th>
      <th align="center" nowrap="nowrap"><?php echo JText::_('Delete All?');?></th>
      <th align="center" nowrap="nowrap"><?php echo JText::_('Upload Images?');?></th>
      <th align="center" nowrap="nowrap"><?php echo JText::_('Upload Files?');?></th>
      <th align="center" nowrap="nowrap"><?php echo JText::_('Create Own Extras?');?></th>
      <th align="center" nowrap="nowrap"><?php echo JText::_('Create Global Extras?');?></th>
      <th align="center" nowrap="nowrap"><?php echo JText::_('Max Extras?');?></th>
   </tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="16">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
    <?php
    $k=0;
    $i=0;
    foreach ($this->users as $row ) {
				?>
    <tr class="<?php echo "row$k"; ?>">
	<td width="20">
		<input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id;?>" onclick="isChecked(this.checked);" />
	</td>
	<td>
		<a href="#edit" onclick="hideMainMenu(); return listItemTask('cb<?php echo $i;?>','user.edit');"><?php echo $row->jname; ?></a>
	</td>
	<td>
		<?php echo $row->username; ?>
	</td>
		<?php
		$img = $row->published?'administrator/images/tick.png':'administrator/images/publish_x.png';
		
		$href='';
		if( $row->published>=0 ) {
			$href = '<a href="javascript: void(0);" ';
			$href .= 'onclick="return listItemTask(\'cb' .$i. '\',\'' .($row->published ? 'user.unpublish' : 'user.publish'). '\')">';
			$href .= '<img src="' . JURI::root() .$img. '" width="12" height="12" border="0" alt="" />';
			$href .= '</a>';
		}
		else {
			$href = '<img src="' . JURI::root() .$img. '" width="12" height="12" border="0" alt="" />';
		}
		?>
     <td align="center"><?php echo $href;?></td>
		
     <?php
		$img = $row->cancreate?'administrator/images/tick.png':'administrator/images/publish_x.png';
		
		$href='';
		if( $row->cancreate>=0 ) {
			$href = '<a href="javascript: void(0);" ';
			$href .= 'onclick="return listItemTask(\'cb' .$i. '\',\'' .($row->cancreate ? 'user.cannotcreate' : 'user.cancreate'). '\')">';
			$href .= '<img src="' . JURI::root() .$img. '" width="12" height="12" border="0" alt="" />';
			$href .= '</a>';
		}
		else {
			$href = '<img src="' . JURI::root() .$img. '" width="12" height="12" border="0" alt="" />';
		}
		?>
     <td align="center"><?php echo $href;?></td>
     
     <td align="center"><?php echo $row->eventslimit;?></td>
     
     <?php
		$img = $row->canpublishown?'administrator/images/tick.png':'administrator/images/publish_x.png';
		
		$href='';
		if( $row->canpublishown>=0 ) {
			$href = '<a href="javascript: void(0);" ';
			$href .= 'onclick="return listItemTask(\'cb' .$i. '\',\'' .($row->canpublishown ? 'user.cannotpublishown' : 'user.canpublishown'). '\')">';
			$href .= '<img src="' . JURI::root() .$img. '" width="12" height="12" border="0" alt="" />';
			$href .= '</a>';
		}
		else {
			$href = '<img src="' . JURI::root() .$img. '" width="12" height="12" border="0" alt="" />';
		}
		?>
     <td align="center"><?php echo $href;?></td>
     
		<?php
		$img = $row->candeleteown?'administrator/images/tick.png':'administrator/images/publish_x.png';
		
		$href='';
		if( $row->candeleteown>=0 ) {
			$href = '<a href="javascript: void(0);" ';
			$href .= 'onclick="return listItemTask(\'cb' .$i. '\',\'' .($row->candeleteown ? 'user.cannotdeleteown' : 'user.candeleteown'). '\')">';
			$href .= '<img src="' . JURI::root() .$img. '" width="12" height="12" border="0" alt="" />';
			$href .= '</a>';
		}
		else {
			$href = '<img src="' . JURI::root() .$img. '" width="12" height="12" border="0" alt="" />';
		}
		?>
     <td align="center"><?php echo $href;?></td>

     <?php
		$img = $row->canedit?'administrator/images/tick.png':'administrator/images/publish_x.png';
		
		$href='';
		if( $row->canedit>=0 ) {
			$href = '<a href="javascript: void(0);" ';
			$href .= 'onclick="return listItemTask(\'cb' .$i. '\',\'' .($row->canedit ? 'user.cannotedit' : 'user.canedit'). '\')">';
			$href .= '<img src="' . JURI::root() .$img. '" width="12" height="12" border="0" alt="" />';
			$href .= '</a>';
		}
		else {
			$href = '<img src="' . JURI::root() .$img. '" width="12" height="12" border="0" alt="" />';
		}
		?>
     <td align="center"><?php echo $href;?></td>

     <?php
		$img = $row->canpublishall?'administrator/images/tick.png':'administrator/images/publish_x.png';
		
		$href='';
		if( $row->canpublishall>=0 ) {
			$href = '<a href="javascript: void(0);" ';
			$href .= 'onclick="return listItemTask(\'cb' .$i. '\',\'' .($row->canpublishall ? 'user.cannotpublishall' : 'user.canpublishall'). '\')">';
			$href .= '<img src="' . JURI::root() .$img. '" width="12" height="12" border="0" alt="" />';
			$href .= '</a>';
		}
		else {
			$href = '<img src="' . JURI::root() .$img. '" width="12" height="12" border="0" alt="" />';
		}
		?>
     <td align="center"><?php echo $href;?></td>
     
     <?php
		$img = $row->candeleteall?'administrator/images/tick.png':'administrator/images/publish_x.png';
		
		$href='';
		if( $row->candeleteall>=0 ) {
			$href = '<a href="javascript: void(0);" ';
			$href .= 'onclick="return listItemTask(\'cb' .$i. '\',\'' .($row->candeleteall ? 'user.cannotdeleteall' : 'user.candeleteall'). '\')">';
			$href .= '<img src="' . JURI::root() .$img. '" width="12" height="12" border="0" alt="" />';
			$href .= '</a>';
		}
		else {
			$href = '<img src="' . JURI::root() .$img. '" width="12" height="12" border="0" alt="" />';
		}
		?>
     <td align="center"><?php echo $href;?></td>
     
     <?php
		$img = $row->canuploadimages?'administrator/images/tick.png':'administrator/images/publish_x.png';
		
		$href='';
		if( $row->canuploadimages>=0 ) {
			$href = '<a href="javascript: void(0);" ';
			$href .= 'onclick="return listItemTask(\'cb' .$i. '\',\'' .($row->canuploadimages ? 'user.cannotuploadimages' : 'user.canuploadimages'). '\')">';
			$href .= '<img src="' . JURI::root() .$img. '" width="12" height="12" border="0" alt="" />';
			$href .= '</a>';
		}
		else {
			$href = '<img src="' . JURI::root() .$img. '" width="12" height="12" border="0" alt="" />';
		}
		?>
     <td align="center"><?php echo $href;?></td>

     <?php
		$img = $row->canuploadmovies?'administrator/images/tick.png':'administrator/images/publish_x.png';
		
		$href='';
		if( $row->canuploadmovies>=0 ) {
			$href = '<a href="javascript: void(0);" ';
			$href .= 'onclick="return listItemTask(\'cb' .$i. '\',\'' .($row->canuploadmovies ? 'user.cannotuploadmovies' : 'user.canuploadmovies'). '\')">';
			$href .= '<img src="' . JURI::root() .$img. '" width="12" height="12" border="0" alt="" />';
			$href .= '</a>';
		}
		else {
			$href = '<img src="' . JURI::root() .$img. '" width="12" height="12" border="0" alt="" />';
		}
		?>
     <td align="center"><?php echo $href;?></td>

     <?php
		$img = $row->cancreateown?'administrator/images/tick.png':'administrator/images/publish_x.png';
		
		$href='';
		if( $row->cancreateown>=0 ) {
			$href = '<a href="javascript: void(0);" ';
			$href .= 'onclick="return listItemTask(\'cb' .$i. '\',\'' .($row->cancreateown ? 'user.cannotcreateown' : 'user.cancreateown'). '\')">';
			$href .= '<img src="' . JURI::root() .$img. '" width="12" height="12" border="0" alt="" />';
			$href .= '</a>';
		}
		else {
			$href = '<img src="' . JURI::root() .$img. '" width="12" height="12" border="0" alt="" />';
		}
		?>
     <td align="center"><?php echo $href;?></td>

     <?php
		$img = $row->cancreateglobal?'administrator/images/tick.png':'administrator/images/publish_x.png';
		
		$href='';
		if( $row->cancreateglobal>=0 ) {
			$href = '<a href="javascript: void(0);" ';
			$href .= 'onclick="return listItemTask(\'cb' .$i. '\',\'' .($row->cancreateglobal ? 'user.cannotcreateglobal' : 'user.cancreateglobal'). '\')">';
			$href .= '<img src="' . JURI::root() .$img. '" width="12" height="12" border="0" alt="" />';
			$href .= '</a>';
		}
		else {
			$href = '<img src="' . JURI::root() .$img. '" width="12" height="12" border="0" alt="" />';
		}
		?>
     <td align="center"><?php echo $href;?></td>

     <td align="center"><?php echo $row->extraslimit;?></td>

     <?php
			$k = 1 - $k;
			$i++;
		?>
	</tr>
		<?php  } ?>
	</tbody>
	</table>
    <?php echo JHTML::_( 'form.token' ); 
?>
<input type="hidden" name="hidemainmenu" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="task" value='user.overview' />
</form>
<script  type="text/javascript" src="<?php echo JURI::root();?>includes/js/overlib_mini.js"></script>
<script language="javascript" type="text/javascript">
function submitbutton(pressbutton) {
	var form = document.getElementsByName ('adminForm');
	<?php
	if( isset($editorFields) && is_array($editorFields) ) {
		foreach ($editorFields as $editor) {
			// Where editor[0] = your areaname and editor[1] = the field name
			echo $wysiwygeditor->save( $editor[1]);
		}
	}
	?>
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	} else {
		submitform( pressbutton );
	}
}
</script>
