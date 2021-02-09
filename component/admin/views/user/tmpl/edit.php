<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: edit.php 2749 2011-10-13 08:54:34Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;

global $task;
$option = JEV_COM_COMPONENT;
$index  = Route::_("index.php");
?>
<script type="text/javascript">
    <!--
    Joomla.submitbutton = function (pressbutton) {
        if (pressbutton.substr(0, 6) == 'cancel' || (pressbutton == 'user.overview')) {
            Joomla.submitform(pressbutton);
            return;
        }
        var form = document.adminForm;
        // do field validation
        if (form.user_id.value == -1) {
            alert("<?php echo Text::_('MISSING_USER_SELECTION'); ?>");
        }
        else {
            Joomla.submitform(pressbutton);
        }
    }
    //-->
</script>

<form action="<?php echo $index; ?>" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="cid" value="<?php echo $this->jevuser->id; ?>"/>
	<table border="0" cellpadding="2" cellspacing="2" class="adminform">
		<tr>
			<td width="20%"><?php echo Text::_('USERNAME'); ?></td>
			<td><?php echo $this->users; ?></td>
		</tr>
		<tr>
			<td><?php echo Text::_('USER_ENABLED'); ?></td>
			<td>
				<fieldset class="radio gsl-button-group">
					<input type="radio" id="jform_published0" name="published" value="0"
						<?php echo !$this->jevuser->published ? 'checked="checked"' : '';?>
					       class="gsl-button-small gsl-radio gsl-hidden">
					<label for="jform_published0" class="gsl-button-small gsl-button gsl-button-<?php echo !$this->jevuser->published ? 'danger' : 'default';?>"><?php echo Text::_("JNO");?></label>
					<input type="radio" id="jform_published1" name="published" value="1"
					       <?php echo $this->jevuser->published ? 'checked="checked"' : '';?> class="gsl-button-small gsl-radio gsl-hidden">
					<label for="jform_published1" class="gsl-button-small gsl-button  gsl-button-<?php echo $this->jevuser->published ? 'primary' : 'default';?>"><?php echo Text::_("JYES");?></label>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td><?php echo Text::_('CAN_CREATE_EVENTS'); ?></td>
			<td>
				<fieldset class="radio gsl-button-group">
					<input type="radio" id="jform_cancreate0" name="cancreate" value="0"
						<?php echo !$this->jevuser->cancreate ? 'checked="checked"' : '';?>
						   class="gsl-button-small gsl-radio gsl-hidden">
					<label for="jform_cancreate0" class="gsl-button-small gsl-button gsl-button-<?php echo !$this->jevuser->cancreate ? 'danger' : 'default';?>"><?php echo Text::_("JNO");?></label>
					<input type="radio" id="jform_cancreate1" name="cancreate" value="1"
						<?php echo $this->jevuser->cancreate ? 'checked="checked"' : '';?> class="gsl-button-small gsl-radio gsl-hidden">
					<label for="jform_cancreate1" class="gsl-button-small gsl-button  gsl-button-<?php echo $this->jevuser->cancreate ? 'primary' : 'default';?>"><?php echo Text::_("JYES");?></label>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td><?php echo Text::_('EVENTS_LIMIT'); ?></td>
			<td>
				<input type="text" size="15" name="eventslimit" id="eventslimit"
				       value="<?php echo $this->jevuser->eventslimit; ?>"/>
			</td>
		</tr>
		<tr>
			<td><?php echo Text::_('CAN_PUBLISH_OWN'); ?></td>
			<td>
				<fieldset class="radio gsl-button-group">
					<input type="radio" id="jform_canpublishown0" name="canpublishown" value="0"
						<?php echo !$this->jevuser->canpublishown ? 'checked="checked"' : '';?>
						   class="gsl-button-small gsl-radio gsl-hidden">
					<label for="jform_canpublishown0" class="gsl-button-small gsl-button gsl-button-<?php echo !$this->jevuser->canpublishown ? 'danger' : 'default';?>"><?php echo Text::_("JNO");?></label>
					<input type="radio" id="jform_canpublishown1" name="canpublishown" value="1"
						<?php echo $this->jevuser->canpublishown ? 'checked="checked"' : '';?> class="gsl-button-small gsl-radio gsl-hidden">
					<label for="jform_canpublishown1" class="gsl-button-small gsl-button  gsl-button-<?php echo $this->jevuser->canpublishown ? 'primary' : 'default';?>"><?php echo Text::_("JYES");?></label>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td><?php echo Text::_('CAN_DELETE_OWN_EVENTS'); ?></td>
			<td>
				<fieldset class="radio gsl-button-group">
					<input type="radio" id="jform_candeleteown0" name="candeleteown" value="0"
						<?php echo !$this->jevuser->candeleteown ? 'checked="checked"' : '';?>
						   class="gsl-button-small gsl-radio gsl-hidden">
					<label for="jform_candeleteown0" class="gsl-button-small gsl-button gsl-button-<?php echo !$this->jevuser->candeleteown ? 'danger' : 'default';?>"><?php echo Text::_("JNO");?></label>
					<input type="radio" id="jform_candeleteown1" name="candeleteown" value="1"
						<?php echo $this->jevuser->candeleteown ? 'checked="checked"' : '';?> class="gsl-button-small gsl-radio gsl-hidden">
					<label for="jform_candeleteown1" class="gsl-button-small gsl-button  gsl-button-<?php echo $this->jevuser->candeleteown ? 'primary' : 'default';?>"><?php echo Text::_("JYES");?></label>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td><?php echo Text::_('CAN_EDIT_EVENTS'); ?></td>
			<td>
				<fieldset class="radio gsl-button-group">
					<input type="radio" id="jform_canedit0" name="canedit" value="0"
						<?php echo !$this->jevuser->canedit ? 'checked="checked"' : '';?>
						   class="gsl-button-small gsl-radio gsl-hidden">
					<label for="jform_canedit0" class="gsl-button-small gsl-button gsl-button-<?php echo !$this->jevuser->canedit ? 'danger' : 'default';?>"><?php echo Text::_("JNO");?></label>
					<input type="radio" id="jform_canedit1" name="canedit" value="1"
						<?php echo $this->jevuser->canedit ? 'checked="checked"' : '';?> class="gsl-button-small gsl-radio gsl-hidden">
					<label for="jform_canedit1" class="gsl-button-small gsl-button  gsl-button-<?php echo $this->jevuser->canedit ? 'primary' : 'default';?>"><?php echo Text::_("JYES");?></label>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td><?php echo Text::_('CAN_PUBLISH_ALL'); ?></td>
			<td>
				<fieldset class="radio gsl-button-group">
					<input type="radio" id="jform_canpublishall0" name="canpublishall" value="0"
						<?php echo !$this->jevuser->canpublishall ? 'checked="checked"' : '';?>
						   class="gsl-button-small gsl-radio gsl-hidden">
					<label for="jform_canpublishall0" class="gsl-button-small gsl-button gsl-button-<?php echo !$this->jevuser->canpublishall ? 'danger' : 'default';?>"><?php echo Text::_("JNO");?></label>
					<input type="radio" id="jform_canpublishall1" name="canpublishall" value="1"
						<?php echo $this->jevuser->canpublishall ? 'checked="checked"' : '';?> class="gsl-button-small gsl-radio gsl-hidden">
					<label for="jform_canpublishall1" class="gsl-button-small gsl-button  gsl-button-<?php echo $this->jevuser->canpublishall ? 'primary' : 'default';?>"><?php echo Text::_("JYES");?></label>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td><?php echo Text::_('CAN_DELETE_ALL_EVENTS'); ?></td>
			<td>
				<fieldset class="radio gsl-button-group">
					<input type="radio" id="jform_candeleteall0" name="candeleteall" value="0"
						<?php echo !$this->jevuser->candeleteall ? 'checked="checked"' : '';?>
						   class="gsl-button-small gsl-radio gsl-hidden">
					<label for="jform_candeleteall0" class="gsl-button-small gsl-button gsl-button-<?php echo !$this->jevuser->candeleteall ? 'danger' : 'default';?>"><?php echo Text::_("JNO");?></label>
					<input type="radio" id="jform_candeleteall1" name="candeleteall" value="1"
						<?php echo $this->jevuser->candeleteall ? 'checked="checked"' : '';?> class="gsl-button-small gsl-radio gsl-hidden">
					<label for="jform_candeleteall1" class="gsl-button-small gsl-button  gsl-button-<?php echo $this->jevuser->candeleteall ? 'primary' : 'default';?>"><?php echo Text::_("JYES");?></label>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td><?php echo Text::_('CAN_UPLOAD_IMAGES'); ?></td>
			<td>
				<fieldset class="radio gsl-button-group">
					<input type="radio" id="jform_canuploadimages0" name="canuploadimages" value="0"
						<?php echo !$this->jevuser->canuploadimages ? 'checked="checked"' : '';?>
						   class="gsl-button-small gsl-radio gsl-hidden">
					<label for="jform_canuploadimages0" class="gsl-button-small gsl-button gsl-button-<?php echo !$this->jevuser->canuploadimages ? 'danger' : 'default';?>"><?php echo Text::_("JNO");?></label>
					<input type="radio" id="jform_canuploadimages1" name="canuploadimages" value="1"
						<?php echo $this->jevuser->canuploadimages ? 'checked="checked"' : '';?> class="gsl-button-small gsl-radio gsl-hidden">
					<label for="jform_canuploadimages1" class="gsl-button-small gsl-button  gsl-button-<?php echo $this->jevuser->canuploadimages ? 'primary' : 'default';?>"><?php echo Text::_("JYES");?></label>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td><?php echo Text::_("UPLOAD_FILES"); ?></td>
			<td>
				<fieldset class="radio gsl-button-group">
					<input type="radio" id="jform_canuploadmovies0" name="canuploadmovies" value="0"
						<?php echo !$this->jevuser->canuploadmovies ? 'checked="checked"' : '';?>
						   class="gsl-button-small gsl-radio gsl-hidden">
					<label for="jform_canuploadmovies0" class="gsl-button-small gsl-button gsl-button-<?php echo !$this->jevuser->canuploadmovies ? 'danger' : 'default';?>"><?php echo Text::_("JNO");?></label>
					<input type="radio" id="jform_canuploadmovies1" name="canuploadmovies" value="1"
						<?php echo $this->jevuser->canuploadmovies ? 'checked="checked"' : '';?> class="gsl-button-small gsl-radio gsl-hidden">
					<label for="jform_canuploadmovies1" class="gsl-button-small gsl-button  gsl-button-<?php echo $this->jevuser->canuploadmovies ? 'primary' : 'default';?>"><?php echo Text::_("JYES");?></label>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td><?php echo Text::_("CREATE_OWN_EXTRAS"); ?></td>
			<td>
				<fieldset class="radio gsl-button-group">
					<input type="radio" id="jform_cancreateown0" name="cancreateown" value="0"
						<?php echo !$this->jevuser->cancreateown ? 'checked="checked"' : '';?>
						   class="gsl-button-small gsl-radio gsl-hidden">
					<label for="jform_cancreateown0" class="gsl-button-small gsl-button gsl-button-<?php echo !$this->jevuser->cancreateown ? 'danger' : 'default';?>"><?php echo Text::_("JNO");?></label>
					<input type="radio" id="jform_cancreateown1" name="cancreateown" value="1"
						<?php echo $this->jevuser->cancreateown ? 'checked="checked"' : '';?> class="gsl-button-small gsl-radio gsl-hidden">
					<label for="jform_cancreateown1" class="gsl-button-small gsl-button  gsl-button-<?php echo $this->jevuser->cancreateown ? 'primary' : 'default';?>"><?php echo Text::_("JYES");?></label>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td><?php echo Text::_("CREATE_GLOBAL_EXTRAS"); ?></td>
			<td>
				<fieldset class="radio gsl-button-group">
					<input type="radio" id="jform_cancreateglobal0" name="cancreateglobal" value="0"
						<?php echo !$this->jevuser->cancreateglobal ? 'checked="checked"' : '';?>
						   class="gsl-button-small gsl-radio gsl-hidden">
					<label for="jform_cancreateglobal0" class="gsl-button-small gsl-button gsl-button-<?php echo !$this->jevuser->cancreateglobal ? 'danger' : 'default';?>"><?php echo Text::_("JNO");?></label>
					<input type="radio" id="jform_cancreateglobal1" name="cancreateglobal" value="1"
						<?php echo $this->jevuser->cancreateglobal ? 'checked="checked"' : '';?> class="gsl-button-small gsl-radio gsl-hidden">
					<label for="jform_cancreateglobal1" class="gsl-button-small gsl-button  gsl-button-<?php echo $this->jevuser->cancreateglobal ? 'primary' : 'default';?>"><?php echo Text::_("JYES");?></label>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td><?php echo Text::_('EXTRAS_LIMIT'); ?></td>
			<td>
				<input type="text" size="15" name="extraslimit" id="extraslimit"
				       value="<?php echo $this->jevuser->extraslimit; ?>"/>
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
					<legend><?php echo Text::_('JEV_APPLICABLE_CATEGORIES'); ?></legend>
					<table class="admintable" cellspacing="1">
						<tr>
							<td valign="top" class="key"><?php echo Text::_('JEV_Categories'); ?>:</td>
							<td><?php if ($this->jevuser->categories == 'all' || $this->jevuser->categories == '') { ?>
									<label for="categories-all">
										<input id="categories-all" type="radio" name="categories" value="all"
										       onclick="allselections('categories');" checked="checked"/>
										<?php echo Text::_('JEV_All'); ?></label>
									<label for="categories-select">
										<input id="categories-select" type="radio" name="categories" value="select"
										       onclick="enableselections('categories');"/>
										<?php echo Text::_('JEV_Select_From_List'); ?></label>
								<?php }
								else
								{ ?>
									<label for="categories-all">
										<input id="categories-all" type="radio" name="categories" value="all"
										       onclick="allselections('categories');"/>
										<?php echo Text::_('JEV_All'); ?></label>
									<label for="categories-select">
										<input id="categories-select" type="radio" name="categories" value="select"
										       onclick="enableselections('categories');" checked="checked"/>
										<?php echo Text::_('JEV_Select_From_List'); ?></label>
								<?php } ?></td>
						</tr>
						<tr>
							<td class="paramlist_key" width="40%"><span class="editlinktip">
					      <label for="categories"
					             id="categories-lbl"><?php echo Text::_('JEV_Categories_selection'); ?></label>
					      </span></td>
							<td><?php echo $this->lists['categories']; ?></td>
						</tr>
					</table>
					<?php if ($this->jevuser->categories == 'all' || $this->jevuser->categories == '') { ?>
						<script type="text/javascript">allselections('categories');</script>
					<?php } ?>
				</fieldset>
			</td>
			<td width="50%">

				<fieldset class="adminform">
					<legend><?php echo Text::_('JEV_APPLICABLE_CALENDARS'); ?></legend>
					<table class="admintable" cellspacing="1">
						<tr>
							<td valign="top" class="key"><?php echo Text::_('JEV_Calendars'); ?>:</td>
							<td><?php if ($this->jevuser->calendars == 'all' || $this->jevuser->calendars == '') { ?>
									<label for="calendars-all">
										<input id="calendars-all" type="radio" name="calendars" value="all"
										       onclick="allselections('calendars');" checked="checked"/>
										<?php echo Text::_('JEV_All'); ?></label>
									<label for="calendars-select">
										<input id="calendars-select" type="radio" name="calendars" value="select"
										       onclick="enableselections('calendars');"/>
										<?php echo Text::_('JEV_Select_From_List'); ?></label>
								<?php }
								else
								{ ?>
									<label for="calendars-all">
										<input id="calendars-all" type="radio" name="calendars" value="all"
										       onclick="allselections('calendars');"/>
										<?php echo Text::_('JEV_All'); ?></label>
									<label for="calendars-select">
										<input id="calendars-select" type="radio" name="calendars" value="select"
										       onclick="enableselections('calendars');" checked="checked"/>
										<?php echo Text::_('JEV_Select_From_List'); ?></label>
								<?php } ?></td>
						</tr>
						<tr>
							<td class="paramlist_key" width="40%"><span class="editlinktip">
					      <label for="calendars"
					             id="calendars-lbl"><?php echo Text::_('JEV_Calendars_selection'); ?></label>
					      </span></td>
							<td><?php echo $this->lists['calendars']; ?></td>
						</tr>
					</table>
					<?php if ($this->jevuser->calendars == 'all' || $this->jevuser->calendars == '') { ?>
						<script type="text/javascript">allselections('calendars');</script>
					<?php } ?>
				</fieldset>
			</td>
		</tr>
	</table>

	<input type="hidden" name="hidemainmenu" value=""/>
	<input type="hidden" name="task" value="<?php echo $task; ?>"/>
	<input type="hidden" name="option" value="<?php echo $option; ?>"/>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
