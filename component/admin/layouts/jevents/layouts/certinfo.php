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
<td class="certinfo">

    <span id="certinfo<?php echo $item->id; ?>">
				<?php if (!$secureUrl)
				{
					$tooltip = ' data-yspoptitle = "' . \JText::_('COM_YOURSITES_SITE_INSECURE', true) . '"'
						. '  data-yspopcontent = "' . JText::_("COM_YOURSITES_SITE_APPEARS_TO_HAVE_INSECURE_CONNECTION", true) . '" '
						. ' data-yspopoptions = \'{"mode" : "hover", "offset" : 20,"delayHide" : 200, "pos" : "top"}\'';
					?>
                    <span class="icon-unlock hasYsPopover sitenotsecure"
                          aria-hidden="true" <?php echo $tooltip; ?> >
                    </span>
					<?php echo JText::_('COM_YOURSITES_SITE_INSECURE', true); ?>
				<?php }
				else if (isset($item->advisorydata) && !empty($item->advisorydata->certexpires))
				{
				$tooltip = '  data-yspoptitle = "' . JText::_('COM_YOURSITES_SITE_CERTIFICATE_EXPIRATION', true)
					. '"  data-yspopcontent = "' . JText::sprintf("COM_YOURSITES_ADVCHECK_CHECKCERTIFICATE_INFO", $item->advisorydata->certexpires, true)
					. (isset($item->advisorydata->certissuer) ? '<br>' . JText::sprintf("COM_YOURSITES_ADVCHECK_CHECKCERTIFICATE_ISSUER_INFO", $item->advisorydata->certissuer, true)  : "") .
                    '" '
					. ' data-yspopoptions = \'{"mode" : "hover", "offset" : 20,"delayHide" : 200, "pos" : "top"}\'';;
				$certexpires = strtotime($item->advisorydata->certexpires);
				$weektime    = strtotime('+1 week');
				$nowtime     = strtotime('+1 second');
				if ($certexpires > $weektime)
				{
					$certexpiresclass = "certmorethanweek";
				}
				else if ($certexpires < $nowtime)
				{
					$certexpiresclass = "certexpired";
				}
				else
				{
					$certexpiresclass = "certexpiressoon";
				}
				?>
                    <span class="siteurlsecure">
                    <span class="icon-key hasYsPopover sitesecure <?php echo $certexpiresclass; ?>"
                          aria-hidden="true" <?php echo $tooltip; ?> >
                    </span>
                        <?php echo JText::sprintf("COM_YOURSITES_ADVCHECK_CHECKCERTIFICATE_INFO", $item->advisorydata->certexpires, true);?>
                        <?php echo (isset($item->advisorydata->certissuer) ? '<br>' . JText::sprintf("COM_YOURSITES_ADVCHECK_CHECKCERTIFICATE_ISSUER_INFO", $item->advisorydata->certissuer, true)  : "") ?>
                <?php }
                ?>
    </span>

</td>

