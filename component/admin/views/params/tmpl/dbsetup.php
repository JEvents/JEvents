<?php 
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: dbsetup.php 1975 2011-04-27 15:52:33Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2018 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');?> 
<div id="jevents">
<form action="index.php" method="post" name="adminForm" id="adminForm">
<h3><?php echo JText::_( 'DATABASE_NOW_SETUP' );?></h3>
<input type="submit" value="<?php echo "continue";?>" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="task" value="cpanel.cpanel" />
<input type="hidden" name="act" value="" />
<input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT;?>" />
</form>
</div>