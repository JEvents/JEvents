<?php 
defined('_JEXEC') or die('Restricted access');

$jinput = JFactory::getApplication()->input;

?>

<table cellpadding="0" cellspacing="0" class="w100 b0">
	<tr class="b0">
		<td align="center" class="w100 b0">
			<form action="<?php echo JRoute::_("index.php?option=".JEV_COM_COMPONENT."&task=search.results&Itemid=".$this->Itemid);?>" method="post" class="search_form">
				<input type="hidden" name="Itemid" value="<?php echo $this->Itemid;?>" />
				<input type="hidden" name="task" value="search.results" />
				<input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT;?>" />

				<input type="text" name="keyword" size="30" maxlength="50" class="inputbox" value="<?php echo $this->keyword;?>" />
				<label for="showpast"><?php echo JText::_("JEV_SHOW_PAST");?></label>
				<input type="checkbox" id="showpast" name="showpast" value="1" <?php echo $jinput->getInt('showpast',0) ? 'checked="checked"' : ''?> />
				<input class="button" type="submit" name="push" value="<?php echo JText::_('JEV_SEARCH_TITLE'); ?>" />
			</form>
		</td>
	</tr>
</table>
