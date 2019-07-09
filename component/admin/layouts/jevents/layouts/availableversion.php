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
<td id="extension_availableversion<?php echo $extension->id; ?>">
	<?php
	echo $this->escape($extension->availableversion);

	if ($extension->update_detailsurl && $extension->availableversion)
	{
		$tooltip = ' data-yspoptitle="' . \JText::_($this->escape($extension->update_name), true) . '"'
			. ' data-yspopcontent="' . \JText::_($this->escape($extension->update_description) . "<br><br>" . JText::_("COM_YOURSITES_CLICK_FOR_EXTENSION_UPDATE_INFORMATION", true), true) . '" '
            . ' data-yspopoptions= \'{"mode" : "hover", "offset" : 20,"delayHide" : 200, "pos" : "top"}\'';
		?>
        <br>
        <div class="extensionUpdateInfo infourl hasYsPopover" <?php echo $tooltip; ?> >
            <a href="<?php echo $this->escape($extension->update_infourl); ?>" target="_blank">
                <span gsl-icon="icon: info" aria-hidden="true"></span>
            </a>
        </div>
		<?php

		$tooltip = ' data-yspoptitle = "' . \JText::_($this->escape($extension->update_name), true) . '"'
			. '  data-yspopcontent="' . \JText::_($this->escape($extension->update_description) . "<br><br>" . JText::_("COM_YOURSITES_CLICK_FOR_EXTENSION_XML_FILE", true), true) . '" '
			. ' data-yspopoptions= \'{"mode" : "hover", "offset" : 20,"delayHide" : 200, "pos" : "top"}\'';
		?>
        <div class="extensionUpdateInfo detailsurl hasYsPopover" <?php echo $tooltip; ?> >
            <a href="<?php echo $this->escape($extension->update_detailsurl); ?>" target="_blank">
                <span gsl-icon="icon: comments" aria-hidden="true"></span>
            </a>
        </div>
		<?php

        if (!$extension->blockupgrade)
        {
	        $params             = JComponentHelper::getParams("com_yoursites");
	        $backupbeforeupdate = $params->get("backupBeforeExtensionUpdate", 0);
	        $siteparams         = new \joomla\Registry\Registry($extension->siteparams);
	        $backupbeforeupdate = intval($siteparams->get("backupBeforeExtensionUpdate", false));

	        if ($backupbeforeupdate)
	        {
		        $tooltip = ' data-yspoptitle = "' . \JText::_($this->escape($extension->update_name), true) . '"'
			        . '  data-yspopcontent="' . JText::_("COM_YOURSITES_BACKUP_AND_UPDATE_EXTENSION", true) . "<br>" . \JText::_($this->escape($extension->update_description), true) . '" '
			        . ' data-yspopoptions= \'{"mode" : "hover", "offset" : 20,"delayHide" : 200, "pos" : "top"}\'';
		        ?>
                <div class="extensionUpdateInfo hasYsPopover" <?php echo $tooltip; ?> >
                    <a href="#progressModal"
                       onclick="document.adminForm['cb<?php echo $i; ?>'].checked = true;backupAndUpgradeExtensions();return false;"
                       title="<?php echo JText::_("COM_YOURSITES_BACKUP_AND_UPDATE_EXTENSION", false); ?>"
                    >
                        <span gsl-icon="icon: bolt" aria-hidden="true"></span>
                        <span></span>
                    </a>
                </div>
		        <?php
	        }
	        else
	        {
		        $tooltip = ' data-yspoptitle = "' . \JText::_($this->escape($extension->update_name), true) . '"'
			        . '  data-yspopcontent="' . JText::_("COM_YOURSITES_UPDATE_EXTENSION", true) . "<br>" . \JText::_($this->escape($extension->update_description), true) . '" '
			        . ' data-yspopoptions= \'{"mode" : "hover", "offset" : 20,"delayHide" : 200, "pos" : "top"}\'';
		        ?>
                <div class="extensionUpdateInfo hasYsPopover" <?php echo $tooltip; ?> >
                    <a href="#progressModal"
                       onclick="document.adminForm['cb<?php echo $i; ?>'].checked = true;upgradeExtensions();return false;"
                       title="<?php echo JText::_("COM_YOURSITES_UPDATE_EXTENSION", false); ?>"
                    >
                        <span gsl-icon="icon: bolt" aria-hidden="true"></span>
                        <span></span>
                    </a>
                </div>
		        <?php
	        }
        }

	}
	?>
</td>
