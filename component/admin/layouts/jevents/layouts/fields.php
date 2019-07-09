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

$lang = JFactory::getLanguage();
$lang->load("com_admin", JPATH_ADMINISTRATOR, null, false, true);

// Add specific helper files for html generation
JHtml::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_admin/helpers/html');

if (!isset($displayData['skiptitle']))
{
?>
<fieldset class="adminform">
    <legend><?php echo JText::_('COM_YOURSITES_CUSTOM_FIELDS'); ?></legend>
	<?php
	}
    ?>
    <table class="table">
        <?php
		if (is_array($item->jcfields) && count($item->jcfields))
		{ ?>
        <thead>
        <tr>
            <th width="25%">
				<?php echo JText::_('COM_YOURSITES_CUSTOM_FIELD_NAME'); ?>
            </th>
            <th>
				<?php echo JText::_('COM_YOURSITES_CUSTOM_FIELD_VALUE'); ?>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php
			foreach ($item->jcfields as $field)
			{
				?>
                <tr>
                    <td>
                        <strong><?php echo $field->title; ?></strong>
                    </td>
                    <td>
						<?php echo $field->value; ?>
                    </td>
                </tr>
				<?php
			}
		} else {
		    echo '<tr><td>' . JText::_("COM_YOURSITES_CUSTOM_FIELD_NONE_CREATED") . '</td></tr>';
        }
		?>
        </tbody>
    </table>
    <?php
    if (!isset($displayData['skiptitle']))
    {
	    ?>
        </fieldset>
	    <?php
    }
