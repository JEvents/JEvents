<?php 
defined('_JEXEC') or die('Restricted access');

function DefaultEventIcalDialog($view,$row, $mask){

        ?>
        <div id="ical_dialog">
        	<div class="ical_dialog_close">
        		<a href="javascript:void(0)" onclick="closeical()">x</a>
        	</div>
        	<?php
        	if ($row->hasRepetition()){
        	?>
        	<div id="unstyledical">
	        	<a href="<?php echo $row->vCalExportLink(false,false);?>" title="<?php echo JText::_("JEV_SAVEICAL")?>">
	        	<?php
	        	echo '<img src="'. JURI::root() . 'components/'.JEV_COM_COMPONENT.'/assets/images/save_f2.png" alt="'.JText::_("JEV_SAVEICAL").'" />';
	             echo JText::_("JEV_All_Recurrences");?>
	             </a><br/>
	        	<a href="<?php echo $row->vCalExportLink(false,true);?>" title="<?php echo JText::_("JEV_SAVEICAL")?>">
	        	<?php
	        	echo '<img src="'. JURI::root() . 'components/'.JEV_COM_COMPONENT.'/assets/images/save_f2.png" alt="'.JText::_("JEV_SAVEICAL").'" />';
	             echo JText::_("JEV_Single_Recurrence");?>
	             </a>
             </div>
        	<div id="styledical">
	        	<a href="<?php echo $row->vCalExportLink(false,false)."&icf=1";?>" title="<?php echo JText::_("JEV_SAVEICAL")?>">
	        	<?php
	        	echo '<img src="'. JURI::root() . 'components/'.JEV_COM_COMPONENT.'/assets/images/save_f2.png" alt="'.JText::_("JEV_SAVEICAL").'" />';
	             echo JText::_("JEV_All_Recurrences");?>
	             </a><br/>
	        	<a href="<?php echo $row->vCalExportLink(false,true)."&icf=1";?>" title="<?php echo JText::_("JEV_SAVEICAL")?>">
	        	<?php
	        	echo '<img src="'. JURI::root() . 'components/'.JEV_COM_COMPONENT.'/assets/images/save_f2.png" alt="'.JText::_("JEV_SAVEICAL").'" />';
	             echo JText::_("JEV_Single_Recurrence");?>
	             </a>
             </div>
             <?php
        	}
        	else {
        	?>
        	<div id="unstyledical">
	        	<a href="<?php echo $row->vCalExportLink(false,false);?>" title="<?php echo JText::_("JEV_EXPORT_EVENT")?>">
	        	<?php
	        	echo '<img src="'. JURI::root() . 'components/'.JEV_COM_COMPONENT.'/assets/images/save_f2.png" alt="'.JText::_("JEV_EXPORT_EVENT").'" />';
	             echo JText::_("JEV_EXPORT_EVENT");?>
	             </a>
             </div>
        	<div id="styledical">
	        	<a href="<?php echo $row->vCalExportLink(false,false)."&icf=1";?>" title="<?php echo JText::_("JEV_EXPORT_EVENT")?>">
	        	<?php
	        	echo '<img src="'. JURI::root() . 'components/'.JEV_COM_COMPONENT.'/assets/images/save_f2.png" alt="'.JText::_("JEV_EXPORT_EVENT").'" />';
	             echo JText::_("JEV_EXPORT_EVENT");?>
	             </a>
             </div>
             <?php
        	}
        	?>
			<label><input name="icf" type="checkbox" value="1" onclick="if(this.checked){$('unstyledical').style.display='none';$('styledical').style.display='block';}else {$('styledical').style.display='none';$('unstyledical').style.display='block';}" /><?php echo JText::_("JEV_PRESERVE_HTML_FORMATTING");?></label>
             
        </div>
        <?php
}

