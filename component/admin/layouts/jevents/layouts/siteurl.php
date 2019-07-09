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
<td class="sitename">
    <a href="<?php echo $this->escape($item->siteurl); ?>" id="siteurl<?php echo $item->id; ?>"
       target="_blank">
		<?php if (!$secureUrl) {
		$tooltip = ' data-yspoptitle = "' . \JText::_('COM_YOURSITES_SITE_INSECURE', true). '"'
			. '  data-yspopcontent = "' . JText::_("COM_YOURSITES_SITE_APPEARS_TO_HAVE_INSECURE_CONNECTION", true) . '" '
			. ' data-yspopoptions = \'{"mode" : "hover", "offset" : 20,"delayHide" : 200, "pos" : "top"}\'';
		?>
        <span class="siteurlnotsecure">
            <span class="icon-unlock hasYsPopover sitenotsecure"
                  aria-hidden="true" <?php echo $tooltip; ?> >
            </span>
        <?php }
        else if(isset($item->advisorydata) && !empty($item->advisorydata->certexpires)) {
        $tooltip = ' data-yspoptitle = "' . \JText::_('COM_YOURSITES_SITE_CERTIFICATE_EXPIRATION', true)
            . '"  data-yspopcontent = "' . \JText::sprintf("COM_YOURSITES_ADVCHECK_CHECKCERTIFICATE_INFO", $item->advisorydata->certexpires, true) . '" '
	        . ' data-yspopoptions = \'{"mode" : "hover", "offset" : 20,"delayHide" : 200, "pos" : "top"}\'';
        $certexpires = strtotime($item->advisorydata->certexpires);
        $weektime = strtotime('+1 week');
        $nowtime = strtotime('+1 second');
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
            <span class="icon-key hasYsPopover sitesecure <?php echo $certexpiresclass;?>"
                  aria-hidden="true" <?php echo $tooltip; ?> >
            </span>
                <?php
                }?>

        <?php
        $siteurl = $item->siteurl;
        if (function_exists("idn_to_utf8"))
        {
            $parts = parse_url($siteurl);

            $utf8host = @idn_to_utf8($parts['host']);
            $siteurl = str_replace($parts['host'], $utf8host, $siteurl);
        }
        echo $this->escape($siteurl);

        if (!$secureUrl) : ?>
        </span>
	<?php endif; ?>
    </a>

    <input type="hidden" id="coretype<?php echo $i; ?>" value="<?php echo $item->coretype; ?>"/>
</td>

