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
	<?php
	$tooltip = ' data-yspoptitle = "' . \JText::_('COM_YOURSITES_SITE_LOGO_FAVICON', true). '"'
		. '  data-yspopcontent = "' . \JText::sprintf("COM_YOURSITES_CLICK_TO_UPDATE_SITE_LOGO", $item->siteurl, true) . '" '
		. ' data-yspopoptions = \'{"mode" : "hover", "offset" : 20,"delayHide" : 200, "pos" : "top"}\'';

	$logourl = $item->params->get('logourl', false);
	$logodata = $item->params->get('logodata', false);

	if ($logodata)
    {
        ?>
        <a class="sitelogo hasYsPopover" href="#"
           onclick="return listItemTask('cb<?php echo $i; ?>', 'sites.fetchlogo');" <?php echo $tooltip; ?>
        >
            <img src="<?php echo $logodata; ?>"
                 alt="<?php echo JText::_('COM_YOURSITES_LOGO_URL'); ?>"
                 class="sitelogo"
                 width="40"
            />
        </a>
        <?php
    }
	else
	{
		if (!$logourl)
		{
			$logourl = "about:blank";
		}
		?>
        <a class="sitelogo hasYsPopover" href="#"
           onclick="return listItemTask('cb<?php echo $i; ?>', 'sites.fetchlogo');" <?php echo $tooltip; ?>
        >
            <img src="<?php echo $logourl; ?>"
                 alt="<?php echo JText::_('COM_YOURSITES_LOGO_URL'); ?>"
                 class="sitelogo"
                 style="display:none;"
                 width="40"
                 onload="this.style.display='inline';"
                 onerror="document.getElementById('joomlaicon<?php echo $i; ?>').style.display='block';"
            />
            <span class="icon-<?php echo $icon; ?>" id="joomlaicon<?php echo $i; ?>"
                  style="display:none;"></span>
        </a>
		<?php
	}
    ?>
</td>
