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

$key = strtolower($sizeitem->id);
if (empty($item->$key))
{
?>
<td class="<?php echo $key;?>">
    <span id="<?php echo $key;?><?php echo $item->id; ?>">
        -
    </span>
</td>

<?php
    return;
}
	?>
    <td class="<?php echo $key; ?>">
        <?php
        if ($key == 'cachesize')
        {
            $title = "COM_YOURSITES_CLEAR_CACHE";
            $action = "clearCache()";
	        $tooltip = ' data-yspoptitle = "' . \JText::_('COM_YOURSITES_CLEAR_CACHE', true) . '"'
		        . ' data-yspopcontent = "' . \JText::_("COM_YOURSITES_CLICK_TO_CLEAR_CACHE", true) . '" '
		        . ' data-yspopoptions = \'{"mode" : "hover", "offset" : 20,"delayHide" : 200, "pos" : "top"}\'';
        }
        else if ($key == "tmpsize")
        {
	        $title = "COM_YOURSITES_CLEAR_TMP";
	        $action = "clearTmp()";
	        $tooltip = ' data-yspoptitle = "' . \JText::_('COM_YOURSITES_CLEAR_TMP', true). '"'
		        . '  data-yspopcontent = "' . \JText::_("COM_YOURSITES_CLEAR_TMP", true) . '" '
		        . ' data-yspopoptions = \'{"mode" : "hover", "offset" : 20,"delayHide" : 200, "pos" : "top"}\'';
        }
        ?>
        <button class="gsl-button gsl-button-small gsl-button-primary hasYsPopover"
           id="<?php echo $key; ?><?php echo $item->id; ?>"
           title="<?php echo JText::_($title, true); ?>"
			<?php echo $tooltip; ?>
           onclick="document.adminForm['cb<?php echo $i; ?>'].checked = true;document.adminForm.boxchecked.value = 1;<?php echo $action;?>;alert(0);"
        >
            <span class="icon-wand" aria-hidden="true"></span>
            <span class="jversion">
            <?php echo htmlspecialchars(!empty($item->$key) ? $item->$key : '?' , ENT_COMPAT, 'UTF-8') . " MB"; ?>
            </span>
        </button>
    </td>

