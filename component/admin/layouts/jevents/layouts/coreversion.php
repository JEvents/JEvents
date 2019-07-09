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
$gslicon = $icon == "wp" ? "wordpress" : $icon;

//$gslicon="emoticon-love";
$params = JComponentHelper::getParams("com_yoursites");

?>
<td>
    <div class="gsl-text-center">
		<?php
		if ($item->coretype == 999)
		{

		}
		else if (empty($item->coreversion)) {

			?>
            <a class="gsl-button gsl-button-small gsl-button-warning ys-version-button hasYsTooltip"
               href="#progressModal"
               onclick="document.adminForm['cb<?php echo $i; ?>'].checked = true;checkJoomlaVersions();return false;"
               title="<?php echo JText::_("COM_YOURSITES_CORE_VERSION_UNKNOWN_INFO", false); ?>"
            >
                <span gsl-icon="icon:<?php echo $gslicon; ?>;ratio:0.8" aria-hidden="true"></span>
                <span class="jversion">?</span>
            </a>
			<?php

		}
		else if (isset($item->availableversion) && version_compare($item->availableversion, $item->coreversion, 'gt')) {

			$tooltip = ' data-yspoptitle = "' . \JText::_('COM_YOURSITES_CORE_VERSION_UPDATE_AVAILABLE', true). '"'
				. '  data-yspopcontent = "' . \JText::_("COM_YOURSITES_CORE_VERSION_CLICK_TO_UPDATE", true) . '" '
				. ' data-yspopoptions = \'{"mode" : "hover", "offset" : 20,"delayHide" : 200, "pos" : "top"}\'';

			// Is this new version blocked?
			$blockedversion = false;
			$globalblock = $params->get( $item->coretype == 1 ? "enablejoomlaupgraderestrictions" : "enablewpupgraderestrictions");
			if ($globalblock == 1)
			{
				$blockedversion = $params->get( $item->coretype == 1 ? "blockedjoomlaversion" : "blockedwpversion", "999.99.99");
			}
			else if ($globalblock == 2)
			{
				$specificblock = $item->params->get( $item->coretype == 1 ? "enablejoomlaupgraderestrictions" : "enablewpupgraderestrictions", false);
				if ($specificblock)
				{
					$blockedversion = $item->params->get( $item->coretype == 1 ? "blockedjoomlaversion" : "blockedwpversion", "999.99.99");
				}
			}
			if ($blockedversion && version_compare($item->availableversion, $blockedversion, 'ge'))
			{
				?>
                <div class="gsl-button gsl-button-small gsl-button-warning updateblocked hasYsPopover"
                     title="<?php echo JText::_("COM_YOURSITES_CORE_VERSION_UPDATE_AVAILABLE_BUT_BLOCKED", false); ?>"
					<?php echo $tooltip; ?>
                >
                    <span gsl-icon="icon:<?php echo $gslicon; ?>;ratio:0.8" aria-hidden="true"></span>
                    <span class="jversion"><?php echo $item->coreversion . " [" . $item->availableversion . "]"; ?></span>
                </div>
				<?php
			}
			else
			{
				?>
                <a class="gsl-button gsl-button-small gsl-button-danger hasYSPopover"
                   href="#progressModal"
                   onclick="document.adminForm['cb<?php echo $i; ?>'].checked = true;upgradeJoomla();return false;"
                   title="<?php echo JText::_("COM_YOURSITES_CORE_VERSION_UPDATE_AVAILABLE", false); ?>"
					<?php echo $tooltip; ?>
                >
                    <span gsl-icon="icon:<?php echo $gslicon; ?>;ratio:0.8" aria-hidden="true"></span>
                    <span class="jversion"><?php echo $item->coreversion . " [" . $item->availableversion . "]"; ?></span>
                </a>
				<?php
			}
		} else {
			if (!$item->pluginversion) {
				?>
                <div class="gsl-button gsl-button-small gsl-button-warning hasYsTooltip"
                     title="<?php echo JText::_("COM_YOURSITES_CORE_VERSION_UP_TO_DATE", false); ?>"
                >
                    <span gsl-icon="icon:<?php echo $gslicon; ?>;ratio:0.8" aria-hidden="true"></span>
                    <span class="jversion"><?php echo $item->coreversion; ?></span>
                </div>
				<?php
			} else {
				if (empty($item->siteInfo)) {
					$btnclass = "gsl-button-primary";
					$tooltip = ' data-yspoptitle = "' . \JText::_('COM_YOURSITES_CORE_VERSION_STATUS_UNKNOWN', true). '"'
						. '  data-yspopcontent = "' . \JText::_("COM_YOURSITES_CLICK_TO_CHECK_FOR_CORE_UPDATES", true) . '" '
						. ' data-yspopoptions = \'{"mode" : "hover", "offset" : 20,"delayHide" : 200, "pos" : "top"}\'';
					$tooltip2 = '<h4 class="tooltiptitle">' . \JText::_('COM_YOURSITES_CORE_VERSION_STATUS_UNKNOWN', true). '</h4>'
						. '<div  class="tooltipbody">' . \JText::_("COM_YOURSITES_CLICK_TO_CHECK_FOR_CORE_UPDATES", true) . '</div>';

				} else {
					$btnclass = "gsl-button-success";
					$tooltip = ' data-yspoptitle = "' . \JText::_('COM_YOURSITES_CORE_VERSION_UP_TO_DATE', true). '"'
						. '  data-yspopcontent = "' . \JText::_("COM_YOURSITES_CLICK_TO_CHECK_FOR_CORE_UPDATES", true) . '" '
						. ' data-yspopoptions = \'{"mode" : "hover", "offset" : 20,"delayHide" : 200, "pos" : "top"}\'';
					$tooltip2 = '<h4 class="tooltiptitle">' . \JText::_('COM_YOURSITES_CORE_VERSION_UP_TO_DATE', true). '</h4>'
						. '<div  class="tooltipbody">' . \JText::_("COM_YOURSITES_CLICK_TO_CHECK_FOR_CORE_UPDATES", true) . '</div>';
				}
				?>
                <a class="gsl-button gsl-button-small gsl-button-primary ys-version-button <?php echo $btnclass; ?>  hasYsPopover"
                   href="#"
                   onclick="document.adminForm['cb<?php echo $i; ?>'].checked = true;checkJoomlaVersions();return false;"
					<?php echo $tooltip; ?>
                >
                    <span gsl-icon="icon:<?php echo $gslicon; ?>;ratio:0.8" aria-hidden="true"></span>
                    <span class="jversion"><?php echo $item->coreversion; ?></span>
                </a>
				<?php
			}
		}
		?>
    </div>
</td>