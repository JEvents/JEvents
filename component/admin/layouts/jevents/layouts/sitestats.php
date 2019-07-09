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

if (isset($item->siteupstats) && !empty($item->siteupstats) && count($item->siteupstats) == 3)
{
	$statswindows = array(
		"0"  => JText::_("COM_YOURSITES_UPTIME_WEEK",true),
		"1"  => JText::_("COM_YOURSITES_UPTIME_MONTH",true),
		"2"  => JText::_("COM_YOURSITES_UPTIME_YEAR",true)
	);
	$hasuptimestats = true;

	if (isset($displayData['skiptitle']))
    {
	    foreach ($statswindows as $window => $label)
	    {
		    echo JText::sprintf($label, $item->siteupstats[$window]) . "<br>";
	    }
    }
    else
    {
	    ?>
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_YOURSITES_UPTIME_STATS'); ?></legend>
		    <?php
		    foreach ($statswindows as $window => $label)
		    {
			    echo JText::sprintf($label, $item->siteupstats[$window]) . "<br>";
		    }
		    ?>
        </fieldset>
	    <?php
    }
}
