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
<td class="sitename">
	<?php

	// extensions/backup views don't allow us to check for site being up
	if (isset($item->isup) && !$item->isup) :
	$tooltip = ' data-yspoptitle = "' . \JText::_('COM_YOURSITES_SITE_INACCESSIBLE', true). '"'
		. '  data-yspopcontent = "' . \JText::sprintf("COM_YOURSITES_SITE_APPEARS_TO_BE_INACCESSIBLE_CLICK_TO_CHECK_AGAIN", $item->siteurl, true) . '" '
		. ' data-yspopoptions = \'{"mode" : "hover", "offset" : 20,"delayHide" : 200, "pos" : "top"}\'';
	?>
    <div class="site-down">
        <a class="sitenotup"
           href="#progressModal"
           onclick="document.adminForm['cb<?php echo $i; ?>'].checked = true;checksiteup();return false;"
        >
                                        <span class="icon-warning hasYsPopover"
                                              aria-hidden="true" <?php echo $tooltip; ?> ></span>
        </a>
		<?php elseif (isset($item->isup) && $item->isup == 2) :
		$tooltip = ' data-yspoptitle = "' . \JText::_('COM_YOURSITES_SITE_OFFLINE', true). '"'
			. '  data-yspopcontent = "' . \JText::sprintf("COM_YOURSITES_SITE_APPEARS_TO_BE_OFFLINE_CLICK_TO_CHECK_AGAIN", $item->siteurl, true) . '" '
			. ' data-yspopoptions = \'{"mode" : "hover", "offset" : 20,"delayHide" : 200, "pos" : "top"}\'';
		?>
        <a class="siteofline"
           href="#progressModal"
           onclick="document.adminForm['cb<?php echo $i; ?>'].checked = true;checksiteup();return false;"
        >
                                        <span class="icon-ban-circle hasYsPopover"
                                              aria-hidden="true" <?php echo $tooltip; ?> ></span>
        </a>
        <div class="site-offline">
			<?php endif; ?>
                <span id="sitename<?php echo $item->id; ?>">
                                        <?php echo $this->escape($item->sitename); ?>
                                    </span>
			<?php if (isset($item->isup) && !$item->isup) : ?>
        </div>
	<?php endif; ?>

        <input type="hidden" id="coretype<?php echo $i;?>" value="<?php echo $item->coretype;?>" />
</td>

