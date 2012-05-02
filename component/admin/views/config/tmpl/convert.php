<?php 
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: convert.php 1975 2011-04-27 15:52:33Z geraintedwards $
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
<h3><?php echo JText::_( 'EVENTS_MIGRATED' );?></h3>
	<?php
}

	?>

</div>