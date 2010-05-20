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
<form action="index.php" method="post" name="adminForm" >
<h3><?php echo JText::_("Database now setup");?></h3>
<input type="submit" value="<?php echo "continue";?>" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="task" value="cpanel.cpanel" />
<input type="hidden" name="act" value="" />
<input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT;?>" />
</form>
</div>