<?php 
defined('_JEXEC') or die('Restricted access');
$jinput = JFactory::getApplication()->input;
?>

<div class="jev_pagination">
	<form action="<?php echo JRoute::_("index.php?option=".JEV_COM_COMPONENT."&task=search.results&Itemid=".$this->Itemid);?>" method="post">
		<input type="text" name="keyword" size="30" maxlength="50" class="inputbox" value="<?php echo $this->keyword;?>" />
		<label for="showpast"><?php echo JText::_("JEV_SHOW_PAST");?></label>
		<input type="checkbox" id="showpast" name="showpast" value="1" <?php echo $jinput->getInt('showpast',0) ? 'checked="checked"' : ''?> />
		<input class="button" type="submit" name="push" value="<?php echo JText::_('JEV_SEARCH_TITLE'); ?>" />
		<br />
		<br />
	</form>
</div>
