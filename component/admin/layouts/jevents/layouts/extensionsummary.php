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
<td class="ext_summary">
	<?php
	if ($item->coretype == 999)
	{

	}
	else if (!$item->totalextensions) {
		if ($item->pluginversion) {
			$message = JText::_("COM_YOURSITES_FIND_EXTENSIONS_AND_CHECK_FOR_UPDATES");
			?>
            <div class="amounts gsl-text-center ysbtn-group">

                <div class="center gsl-button gsl-button-small gsl-button-warning hasYsTooltip"
                     href="#progressModal"
                     onclick="document.adminForm['cb<?php echo $i; ?>'].checked = true;checkExtensionVersions();return false;"
                     title="<?php echo $message ?>">
                    <span class="icon-info" aria-hidden="true"></span>
                    <span class="sitecount"> ? </span>
                </div>
            </div>
			<?php
		} else {
			$message = JText::_("COM_YOURSITES_PLEASE_ENSURE_YOURSITES_PLUGIN_IS_INSTALLED_ON_THIS_SITE_AND_THAT_IT_IS_CONNECTED_TO_THIS_SERVER");
			?>
            <div class="amounts gsl-text-center ysbtn-group">
                <div class="center gsl-button gsl-button-small gsl-button-warning hasYsTooltip"
                     title="<?php echo $message ?>">
                    <span class="icon-info" aria-hidden="true"></span>
                    <span class="sitecount"> ? </span>
                </div>
            </div>
			<?php
		}

	} else {
		?>

        <div class="amounts gsl-text-center ysbtn-group">
            <a class="gsl-button gsl-button-small gsl-button-primary hasYsTooltip"
               href="javascript:void(0);"
               onclick=" return listItemTask('cb<?php echo $i; ?>', 'sites.listextensions')"
               title="<?php echo JText::_("COM_YOURSITES_LIST_EXTENSIONS", false); ?>"
            >
                <span class="icon-menu-3" aria-hidden="true"></span>
                <span class="sitecount">
                                            <?php
                                            echo $item->totalextensionsNotCore;
                                            ?>
                                            </span>
            </a><a class="gsl-button gsl-button-small gsl-button-success hasYsTooltip uptodateextensions"
               href="javascript:void(0);"
               onclick=" return listItemTask('cb<?php echo $i; ?>', 'sites.listuptodateextensions')"
               title="<?php echo JText::_("COM_YOURSITES_LIST_UP_TO_DATE_EXTENSIONS", false); ?>"
            >
                <span class="icon-checkmark" aria-hidden="true"></span>
                <span class="sitecount">
                                            <?php
                                            echo $item->uptodateextensionsNotCore;
                                            ?>
                                            </span>
            </a><?php
			if ($item->outofdateextensions > 0) {
				?><a class="gsl-button gsl-button-small gsl-button-danger hasYsTooltip outofdateextensions"
                   href="javascript:void(0);"
                   onclick=" return listItemTask('cb<?php echo $i; ?>', 'sites.listextensionupdates')"
                   title="<?php echo JText::_("COM_YOURSITES_LIST_EXTENSION_UPDATES", false); ?>"
                >
                    <span class="icon-flash" aria-hidden="true"></span>
                    <span class="sitecount">
                                                    <?php echo $item->outofdateextensions; ?>
                                                </span>
                </a><?php
			} else if ($item->outofdateextensions == 0 && $item->uptodateextensions > 0) {
				?><a class="gsl-button gsl-button-small gsl-button-success hasYsTooltip"
                   href="javascript:void(0);"
                   onclick=" return listItemTask('cb<?php echo $i; ?>', 'sites.listextensionupdates')"
                   title="<?php echo JText::_("COM_YOURSITES_LIST_EXTENSION_UPDATES", false); ?>"
                >
                    <span class="icon-flash" aria-hidden="true"></span>
                    <span class="sitecount"><?php echo $item->outofdateextensions; ?></span>
                </a><?php
			} else {
				?><a class="gsl-button gsl-button-small btn-inverse hasYsTooltip"
                   href="javascript:void(0);"
                   onclick=" return listItemTask('cb<?php echo $i; ?>', 'sites.listextensionupdates')"
                   title="<?php echo JText::_("COM_YOURSITES_LIST_EXTENSION_UPDATES", false); ?>"
                >
                    <span class="icon-question" aria-hidden="true"></span>
                    <span class="sitecount">?</span>
                </a><?php
			}
			?><a class="gsl-button gsl-button-small gsl-button-primary hasYsTooltip"
               href="javascript:void(0);"
               href="#progressModal"
               onclick="document.adminForm['cb<?php echo $i; ?>'].checked = true;checkExtensionVersions();return false;"
               title="<?php echo JText::_("COM_YOURSITES_FIND_EXTENSIONS_AND_CHECK_FOR_UPDATES", false); ?>"
            >
                <span class="icon-search" aria-hidden="true"></span>
                <span></span>
            </a>
        </div>
		<?php
	}
	?>
</td>