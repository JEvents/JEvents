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
<td id="extension_name<?php echo $extension->id; ?>">
	<?php
	if (!$extension->enabled)
	{
		?>
        <span class="disabledextension hasYsTooltip"
              data-original-title="<?php echo JText::_("COM_YOURSITES_EXTENSION_DISABLED_ON_CLIENT_SITE", true); ?>">
                                <span class="disabledextension icon-warning-circle "></span>
			<?php
			echo strip_tags(html_entity_decode($this->escape($extension->name)));
			echo !empty($extension->author) ? '<br>[ ' . $extension->author . ' ]' : '';
			?>
                            </span>
		<?php
	}
	else if (false && !$extension->coresoftware)
	{
		?>
        <span class="enabledextension hasYsTooltip"
              data-original-title="<?php echo JText::_("COM_YOURSITES_EXTENSION_ENABLED_ON_CLIENT_SITE", true); ?>">
                                <span class="enabledextension icon-checkbox "></span>
			<?php
			echo strip_tags(html_entity_decode($this->escape($extension->name)));
			echo !empty($extension->author) ? '<br>[ ' . $extension->author . ' ]' : '';
			?>
                            </span>
		<?php
	}
	else
	{
		echo strip_tags(html_entity_decode($this->escape($extension->name)));
		echo !empty($extension->author) ? '<br>[ ' . $extension->author . ' ]' : '';
	}
	?>
</td>
