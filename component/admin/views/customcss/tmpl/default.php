<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: cpanel.php 3119 2011-12-20 14:34:33Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2018 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

$input = Factory::getApplication()->input;  ?>

<form action="index.php?option=com_jevents&view=customcss" method="post" name="adminForm" id="adminForm"
      class="form-vertical">
	<?php //Render the Editor ?>
	<?php echo $this->form->renderField('source'); ?>
	<?php echo HTMLHelper::_('form.token'); ?>
	<input type="hidden" name="controller" value="component"/>
	<input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT; ?>"/>
	<input type="hidden" name="task" value=""/>
</form>