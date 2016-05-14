<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: edit.php 2749 2011-10-13 08:54:34Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2016 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

global  $task;
$option = JEV_COM_COMPONENT;
$index=JRoute::_("index.php");
?>
<script type="text/javascript" >
	<!--
		Joomla.submitbutton = function (pressbutton) {
if (pressbutton.substr(0, 6) == 'cancel' || (pressbutton == 'user.overview')) {
		submitform( pressbutton );
		return;
	}
	var form = document.adminForm;
	// do field validation
	if (form.user_id.value == -1) {
		alert( "<?php echo JText::_( 'MISSING_USER_SELECTION' ); ?>" );
	}
	else {
		submitform(pressbutton);
	}
}
//-->
</script>

<form action="<?php echo $index;?>" method="post" name="adminForm" id="adminForm">
    <input type="hidden" name="cid" value="<?php echo $this->jevuser->id;?>" />
	<table border="0" cellpadding="2" cellspacing="2" class="adminform" >
		<tr>
			<td width="20%"><?php echo JText::_( 'USERNAME' );?></td>
			<td><?php echo $this->users;?></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'USER_ENABLED' );?></td>
			<td>
				<fieldset class="radio btn-group">
				<?php
			echo JHTML::_("select.booleanlist", "published", null,$this->jevuser->published);
				?>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'CAN_CREATE_EVENTS' );?></td>
			<td>
				<fieldset class="radio btn-group">
				<?php
			echo JHTML::_("select.booleanlist", "cancreate", null,$this->jevuser->cancreate);
				?>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'EVENTS_LIMIT' );?></td>
			<td>
			<input type="text" size="15" name="eventslimit" id="eventslimit" value="<?php echo $this->jevuser->eventslimit;?>" />
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'CAN_PUBLISH_OWN' );?></td>
			<td>
				<fieldset class="radio btn-group">
				<?php
				echo JHTML::_("select.booleanlist", "canpublishown", null,$this->jevuser->canpublishown);
				?>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'CAN_DELETE_OWN_EVENTS' );?></td>
			<td>
				<fieldset class="radio btn-group">
				<?php
				echo JHTML::_("select.booleanlist", "candeleteown", null,$this->jevuser->candeleteown);
				?>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'CAN_EDIT_EVENTS' );?></td>
			<td>
				<fieldset class="radio btn-group">
				<?php
				echo JHTML::_("select.booleanlist", "canedit", null,$this->jevuser->canedit);
				?>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'CAN_PUBLISH_ALL' );?></td>
			<td>
				<fieldset class="radio btn-group">
				<?php
				echo JHTML::_("select.booleanlist", "canpublishall", null,$this->jevuser->canpublishall);
				?>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'CAN_DELETE_ALL_EVENTS' );?></td>
			<td>
				<fieldset class="radio btn-group">
				<?php
				echo JHTML::_("select.booleanlist", "candeleteall", null,$this->jevuser->candeleteall);
				?>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'CAN_UPLOAD_IMAGES' );?></td>
			<td>
				<fieldset class="radio btn-group">
				<?php
				echo JHTML::_("select.booleanlist", "canuploadimages", null,$this->jevuser->canuploadimages);
				?>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_("UPLOAD_FILES");?></td>
			<td>
				<fieldset class="radio btn-group">
				<?php
				echo JHTML::_("select.booleanlist", "canuploadmovies", null,$this->jevuser->canuploadmovies);
				?>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_("CREATE_OWN_EXTRAS");?></td>
			<td>
				<fieldset class="radio btn-group">
				<?php
				echo JHTML::_("select.booleanlist", "cancreateown", null,$this->jevuser->cancreateown);
				?>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_("CREATE_GLOBAL_EXTRAS");?></td>
			<td>
				<fieldset class="radio btn-group">
				<?php
				echo JHTML::_("select.booleanlist", "cancreateglobal", null,$this->jevuser->cancreateglobal);
				?>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'EXTRAS_LIMIT' );?></td>
			<td>
			<input type="text" size="15" name="extraslimit" id="extraslimit" value="<?php echo $this->jevuser->extraslimit;?>" />
			</td>
		</tr>
	
    </table>
	<script type="text/javascript">
			function allselections(id) {
				var e = document.getElementById(id);
					e.disabled = true;
				var i = 0;
				var n = e.options.length;
				for (i = 0; i < n; i++) {
					e.options[i].disabled = true;
					e.options[i].selected = true;
				}
			}
			function enableselections(id) {
				var e = document.getElementById(id);
					e.disabled = false;
				var i = 0;
				var n = e.options.length;
				for (i = 0; i < n; i++) {
					e.options[i].disabled = false;
				}
			}
		</script>
	<table class="admintable">
		<tr>
			<td width="50%">
				<fieldset class="adminform useradminform">
					<legend><?php echo JText::_( 'JEV_APPLICABLE_CATEGORIES' ); ?></legend>
					<table class="admintable" cellspacing="1">
					  <tr>
					    <td valign="top" class="key"><?php echo JText::_( 'JEV_Categories' ); ?>: </td>
					    <td><?php if ($this->jevuser->categories == 'all' || $this->jevuser->categories == '') { ?>
					      <label for="categories-all">
					        <input id="categories-all" type="radio" name="categories" value="all" onclick="allselections('categories');" checked="checked" />
					        <?php echo JText::_( 'JEV_All' ); ?></label>
					      <label for="categories-select">
					        <input id="categories-select" type="radio" name="categories" value="select" onclick="enableselections('categories');" />
					        <?php echo JText::_( 'JEV_Select_From_List' ); ?></label>
					      <?php } 
					      else { ?>
					      <label for="categories-all">
					        <input id="categories-all" type="radio" name="categories" value="all" onclick="allselections('categories');" />
					        <?php echo JText::_( 'JEV_All' ); ?></label>
					      <label for="categories-select">
					        <input id="categories-select" type="radio" name="categories" value="select" onclick="enableselections('categories');" checked="checked" />
					        <?php echo JText::_( 'JEV_Select_From_List' ); ?></label>
					      <?php } ?></td>
					  </tr>
					  <tr>
					    <td class="paramlist_key" width="40%"><span class="editlinktip">
					      <label for="categories" id="categories-lbl"><?php echo JText::_('JEV_Categories_selection');?></label>
					      </span></td>
					    <td><?php echo $this->lists['categories'];?></td>
					  </tr>
					</table>
					<?php if ($this->jevuser->categories == 'all'  || $this->jevuser->categories == '') { ?>
					<script type="text/javascript">allselections('categories');</script>
					<?php } ?>
				</fieldset>
			</td>
			<td width="50%">
		
				<fieldset class="adminform">
					<legend><?php echo JText::_( 'JEV_APPLICABLE_CALENDARS' ); ?></legend>
					<table class="admintable" cellspacing="1">
					  <tr>
					    <td valign="top" class="key"><?php echo JText::_( 'JEV_Calendars' ); ?>: </td>
					    <td><?php if ($this->jevuser->calendars == 'all' || $this->jevuser->calendars == '') { ?>
					      <label for="calendars-all">
					        <input id="calendars-all" type="radio" name="calendars" value="all" onclick="allselections('calendars');" checked="checked" />
					        <?php echo JText::_( 'JEV_All' ); ?></label>
					      <label for="calendars-select">
					        <input id="calendars-select" type="radio" name="calendars" value="select" onclick="enableselections('calendars');" />
					        <?php echo JText::_( 'JEV_Select_From_List' ); ?></label>
					      <?php } 
					      else { ?>
					      <label for="calendars-all">
					        <input id="calendars-all" type="radio" name="calendars" value="all" onclick="allselections('calendars');" />
					        <?php echo JText::_( 'JEV_All' ); ?></label>
					      <label for="calendars-select">
					        <input id="calendars-select" type="radio" name="calendars" value="select" onclick="enableselections('calendars');" checked="checked" />
					        <?php echo JText::_( 'JEV_Select_From_List' ); ?></label>
					      <?php } ?></td>
					  </tr>
					  <tr>
					    <td class="paramlist_key" width="40%"><span class="editlinktip">
					      <label for="calendars" id="calendars-lbl"><?php echo JText::_('JEV_Calendars_selection');?></label>
					      </span></td>
					    <td><?php echo $this->lists['calendars'];?></td>
					  </tr>
					</table>
					<?php if ($this->jevuser->calendars == 'all'|| $this->jevuser->calendars == '') { ?>
					<script type="text/javascript">allselections('calendars');</script>
					<?php }  ?>
				</fieldset>
			</td>
		</tr>
	</table>
    
    <input type="hidden" name="hidemainmenu" value="" />
	<input type="hidden" name="task" value="<?php echo $task; ?>" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
	