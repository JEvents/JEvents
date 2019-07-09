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

$user = JFactory::getUser();
$canCreate = $user->authorise('core.create', 'com_yoursites');
$canEdit = $user->authorise('core.edit', 'com_yoursites');
$canCheckin = $user->authorise('core.manage', 'com_yoursites');
$canChange = $user->authorise('core.edit.state', 'com_yoursites');

?>
<td class="row_checkbox">
	<?php
    if (isset($extension))
    {
        ?>
        <input type="checkbox" id="cb<?php echo $i; ?>" name="cid[]" class="gsl-checkbox"
               value="<?php echo $extension->id; ?>" onclick="Joomla.isChecked(this.checked);">
        <input name="site_id[]" id='siteforextension<?php echo $extension->id;?>' value="<?php echo $extension->site_id;?>" type="hidden"
               data-updateavailable="<?php echo !empty($extension->availableversion) ? 1 : 0; ?>"
               data-updateblocked="<?php echo $extension->blockupgrade ? 1 : 0; ?>"
        />
        <!-- I know this could be a duplicate id in the DOM but it is the easiest solution //-->
        <span id="sitename<?php echo $extension->site_id;?>" style="display:none;"><?php echo $this->escape($extension->sitename);?></span>
        <span id="siteurl<?php echo $extension->site_id;?>"  style="display:none;"><?php echo $this->escape($extension->siteurl);?></span>

	    <?php if ($canEdit) : ?>
        <span style="display:none;"
                id="extension_sitename<?php echo $extension->id; ?>">
								<?php echo $this->escape($item->sitename); ?></span>
    <?php else : ?>
        <span style="display:none;"
                id="extension_sitename<?php echo $extension->id; ?>">
						                    <?php echo $this->escape($item->sitename); ?>
					                    </span>
    <?php endif; ?>
        <a style="display:none;" href="<?php echo $item->siteurl; ?>" id="extension_siteurl<?php echo $extension->id; ?>"
           target="_blank"><?php echo $item->siteurl; ?></a>
	    <?php
    }
    else
    {
    ?>
    <input type="checkbox" id="cb<?php echo $i; ?>" name="cid[]" class="gsl-checkbox"
           value="<?php echo $item->id; ?>" onclick="Joomla.isChecked(this.checked);">
	<?php
    }
    ?>
</td>

