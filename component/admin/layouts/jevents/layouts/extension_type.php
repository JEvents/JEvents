<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_admin
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

extract($displayData);

?>
<td>
	<?php
	if (JText::_("COM_YOURSITES_EXTENSIONS_TYPE_" . $extension->type) != $extension->type)
	{
		echo JText::_("COM_YOURSITES_EXTENSIONS_TYPE_" . $extension->type);
	}
	else
	{
		echo $extension->type;
	}
	if (!empty($extension->folder))
	{
		echo "<br> (" . ucwords($this->escape($extension->folder)) . ")";
	}
	?>
</td>
