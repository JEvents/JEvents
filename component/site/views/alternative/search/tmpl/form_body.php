<?php 
defined('_JEXEC') or die('Restricted access');
?>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td align="center" width="100%">
			<form action="<?php echo JRoute::_("index.php?option=".JEV_COM_COMPONENT."&task=search.results&Itemid=".$this->Itemid);?>" method="post" style="font-size:1;">
				<input type="text" name="keyword" size="30" maxlength="50" class="inputbox" value="<?php echo $this->keyword;?>" />
				<label for="showpast"><?php echo JText::_("JEV_SHOW_PAST");?></label>
				<input type="checkbox" id="showpast" name="showpast" value="1" <?php echo JRequest::getInt('showpast',0)?'checked="checked"':''?> />
				<br />
				<input class="button" type="submit" name="push" value="<?php echo JText::_('JEV_SEARCH_TITLE'); ?>" />
			</form>
		</td>
	</tr>
</table>
