<?php 
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: dbsetup.php 1399 2009-03-30 08:31:52Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');?> 
<div id="jevents">
<?php 
if ($this->remaining>0)  { ?>
<h3><?php echo JText::sprintf("Events migrated - %s remain please wait", $this->remaining);?></h3>

<?php
$url = JRoute::_("index.php?option=".JEV_COM_COMPONENT."&task=config.convert&ongoing=1",false);
echo "<script>document.location.href='$url';</script>\n";
}
else {
	?>
<h3><?php echo JText::_("Events migrated");?></h3>
	<?php
}

	?>

</div>