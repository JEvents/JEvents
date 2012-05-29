<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: default_layout.php 3323 2012-03-08 13:37:46Z geraintedwards $
 * @package     JEvents
 * @subpackage  Module JEvents Filter
 * @copyright   Copyright (C) 2008 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.gwesystems.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
if (count($filterHTML)>0){
	JEVHelper::script("mod_jevents_filter.js","modules/mod_jevents_filter/",true);
	?>
	<form action="<?php echo $form_link;?>" id="jeventspost" name="jeventspost" method="post">
	<?php
		// This forces category settings in URL to reset too since they could be set by SEF 
		$script = "try {JeventsFilters.filters.push({id:'catidsfv',value:0});} catch (e) {}\n";
		
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($script);
	?>
	<input type='hidden' name='catids' id='catidsfv' value='<?php echo trim($datamodel->catidsOut);?>' />
	<table cellpadding="0" cellspacing="0" border="0">
	<?php	
	
	foreach ($filterHTML as $filter){
		if (!isset($filter["title"])) continue;
		echo "<tr>";
		if (strlen($filter["title"])>0) echo "<td>".$filter["title"]."</td>";
		else echo "<td/>";
		echo "<td>".$filter["html"]."</td></tr>";
	}
	
	echo "<tr>";
	echo "<td>".'<input class="modfilter_button" type="button" onclick="JeventsFilters.reset(this.form)" value="'.JText::_('RESET').'" />'."</td>";
	echo "<td >".'<input class="modfilter_button" type="submit" value="'.JText::_('ok').'" />'."</td></tr>";
	?>
	</table>
	</form>
	<?php 
}