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
    <div class="center ">
		<?php if (!$extension->blockupgrade) { ?>
            <a class="gsl-button gsl-button-xsmall hasYsTooltip gsl-button-success" href="javascript:void(0);"
               onclick="return listItemTask('cb<?php echo $i; ?>','extensions.lock')" title=""
               data-original-title="<?php echo JText::_("COM_YOURSITES_LOCK_EXTENSION", true); ?>">
                <span class="icon-checkbox-unchecked" aria-hidden="true"></span>
            </a>
			<?php
		}
		else
		{ ?>
            <a class="gsl-button gsl-button-xsmall hasYsTooltip  gsl-button-danger" href="javascript:void(0);"
               onclick="return listItemTask('cb<?php echo $i; ?>','extensions.unlock')" title=""
               data-original-title="<?php echo JText::_("COM_YOURSITES_UNLOCK_EXTENSION", true); ?>">
                <span class="icon-checkbox-checked" aria-hidden="true"></span>
            </a>
			<?php
		}
		/*
		JHtml::_('actionsdropdown.' . ((int) $extension->state === 2 ? 'un' : '') . 'archive', 'cb' . $i, 'sites');
		JHtml::_('actionsdropdown.' . ((int) $extension->state === -2 ? 'un' : '') . 'trash', 'cb' . $i, 'sites');
		echo JHtml::_('actionsdropdown.render', $this->escape($extension->sitename));
		*/
		?>
    </div>
</td>
