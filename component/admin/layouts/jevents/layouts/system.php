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

if ($item->siteInfo && !empty($item->siteInfo))
{
	$item->siteInfo = !is_array($item->siteInfo) ? get_object_vars($item->siteInfo) : $item->siteInfo;

	$lang = JFactory::getLanguage();
	$lang->load("com_admin", JPATH_ADMINISTRATOR, null, false, true);

    // Add specific helper files for html generation
	JHtml::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_admin/helpers/html');

	?>
    <fieldset class="adminform">
        <legend><?php echo JText::_('COM_ADMIN_SYSTEM_INFORMATION'); ?></legend>
        <table class="table">
            <thead>
            <tr>
                <th width="25%">
					<?php echo JText::_('COM_ADMIN_SETTING'); ?>
                </th>
                <th>
					<?php echo JText::_('COM_ADMIN_VALUE'); ?>
                </th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <td colspan="2">&#160;</td>
            </tr>
            </tfoot>
            <tbody>
            <tr>
                <td>
                    <strong><?php echo JText::_('COM_ADMIN_PHP_BUILT_ON'); ?></strong>
                </td>
                <td>
					<?php echo isset($item->siteInfo['php']) ? $item->siteInfo['php'] : ''; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <strong><?php echo JText::_('COM_ADMIN_DATABASE_TYPE'); ?></strong>
                </td>
                <td>
					<?php echo isset($item->siteInfo['dbserver']) ? $item->siteInfo['dbserver'] : ''; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <strong><?php echo JText::_('COM_ADMIN_DATABASE_VERSION'); ?></strong>
                </td>
                <td>
					<?php echo isset($item->siteInfo['dbversion']) ? $item->siteInfo['dbversion'] : ''; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <strong><?php echo JText::_('COM_ADMIN_DATABASE_COLLATION'); ?></strong>
                </td>
                <td>
					<?php echo isset($item->siteInfo['dbcollation']) ? $item->siteInfo['dbcollation'] : ''; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <strong><?php echo JText::_('COM_ADMIN_DATABASE_CONNECTION_COLLATION'); ?></strong>
                </td>
                <td>
					<?php echo isset($item->siteInfo['dbconnectioncollation']) ? $item->siteInfo['dbconnectioncollation'] : ''; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <strong><?php echo JText::_('COM_ADMIN_PHP_VERSION'); ?></strong>
                </td>
                <td>
					<?php echo isset($item->siteInfo['phpversion']) ? $item->siteInfo['phpversion'] : ''; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <strong><?php echo JText::_('COM_ADMIN_WEB_SERVER'); ?></strong>
                </td>
                <td>
					<?php echo isset($item->siteInfo['server']) ? JHtml::_('system.server', $item->siteInfo['server']) : ''; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <strong><?php echo JText::_('COM_ADMIN_WEBSERVER_TO_PHP_INTERFACE'); ?></strong>
                </td>
                <td>
					<?php echo isset($item->siteInfo['sapi_name']) ? $item->siteInfo['sapi_name'] : ''; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <strong><?php echo JText::_('COM_ADMIN_JOOMLA_VERSION'); ?></strong>
                </td>
                <td>
					<?php echo isset($item->siteInfo['version']) ? $item->siteInfo['version'] : ''; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <strong><?php echo JText::_('COM_ADMIN_PLATFORM_VERSION'); ?></strong>
                </td>
                <td>
					<?php echo isset($item->siteInfo['platform']) ? $item->siteInfo['platform'] : ''; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <strong><?php echo JText::_('COM_ADMIN_USER_AGENT'); ?></strong>
                </td>
                <td>
					<?php echo isset($item->siteInfo['useragent']) ? htmlspecialchars($item->siteInfo['useragent'], ENT_COMPAT, 'UTF-8') : ''; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <strong><?php echo JText::_('COM_YOURSITES_DB_SIZE'); ?></strong>
                </td>
                <td>
					<?php
					echo htmlspecialchars((isset($item->siteInfo['dbsize']) ? $item->siteInfo['dbsize'] : '?'), ENT_COMPAT, 'UTF-8');
					?>
                </td>
            </tr>
            <tr>
                <td>
                    <strong><?php echo JText::_('COM_YOURSITES_CACHE_SIZE'); ?></strong>
                </td>
                <td>
					<?php

					$tooltip = ' data-yspoptitle = "' . \JText::_('COM_YOURSITES_CLEAR_CACHE', true). '"'
						. '  data-yspopcontent = "' . \JText::_("COM_YOURSITES_CLICK_TO_CLEAR_CACHE", true) . '" '
						. ' data-yspopoptions = \'{"mode" : "hover", "offset" : 20,"delayHide" : 200, "pos" : "top"}\'';

					?>
                    <button class="gsl-button gsl-button-primary gsl-button-small hasYsPopover"
                       title="<?php echo JText::_("COM_YOURSITES_CLEAR_CACHE", true); ?>"
						<?php echo $tooltip; ?>
                       onclick="document.adminForm['cb<?php echo $i; ?>'].checked = true;document.adminForm.boxchecked.value = 1;clearCache();"
                    >
                        <span gsl-icon="icon: bolt" aria-hidden="true">
                        </span>
                        <span class="jversion">
                            <?php echo htmlspecialchars((isset($item->siteInfo['cacheusage']) ? $item->siteInfo['cacheusage'] : '?'), ENT_COMPAT, 'UTF-8') . " MB"; ?>
                        </span>
                    </button>

                </td>
            </tr>
            <tr>
                <td>
                    <strong><?php echo JText::_('COM_YOURSITES_TMP_SIZE'); ?></strong>
                </td>
                <td>
					<?php

					$tooltip = ' data-yspoptitle = "' . \JText::_('COM_YOURSITES_CLEAR_TMP', true). '"'
						. '  data-yspopcontent = "' . \JText::_("COM_YOURSITES_CLEAR_TMP", true) . '" '
						. ' data-yspopoptions = \'{"mode" : "hover", "offset" : 20,"delayHide" : 200, "pos" : "top"}\'';

					?>
                    <button class="gsl-button gsl-button-primary gsl-button-small hasYsPopover"
                       title="<?php echo JText::_("COM_YOURSITES_CLEAR_TMP", true); ?>"
						<?php echo $tooltip; ?>
                       onclick="document.adminForm['cb<?php echo $i; ?>'].checked = true;document.adminForm.boxchecked.value = 1;clearTmp();"
                    >
                        <span gsl-icon="icon: bolt" aria-hidden="true">
                        </span>
                        <span class="jversion">
                            <?php echo htmlspecialchars((isset($item->siteInfo['tmpusage']) ? $item->siteInfo['tmpusage'] : '?'), ENT_COMPAT, 'UTF-8') . " MB"; ?>
                        </span>
                    </button>

                </td>
            </tr>
            <tr>
                <td>
                    <strong><?php echo JText::_('COM_YOURSITES_PHP_INI_DATA'); ?></strong>
                </td>
                <td>
					<?php
					if (isset($item->siteInfo['inidata']))
					{
						foreach ($item->siteInfo['inidata'] as $k => $v)
						{
							echo "$k => <span class='$k'>$v</span> <br>";
						}
					}
					?>
                </td>
            </tr>
            </tbody>
        </table>
    </fieldset>
	<?php
}
