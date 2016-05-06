<?php 
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: csvimport.php 3548 2012-04-20 09:25:43Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2016 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');

$db	= JFactory::getDBO();
$user = JFactory::getUser();
?>
<form action="index.php" method="post" name="adminForm" enctype="multipart/form-data" id="adminForm">
	<?php
	echo $this->callist."<br/>";
	?>
    <input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT;?>" />
    <input type="hidden" name="task" value="icalevent.csvimport2" />
</form>
