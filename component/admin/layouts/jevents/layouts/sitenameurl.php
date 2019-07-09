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

$user       = JFactory::getUser();
$canCreate  = $user->authorise('core.create', 'com_yoursites');
$canEdit    = $user->authorise('core.edit', 'com_yoursites');
$canCheckin = $user->authorise('core.manage', 'com_yoursites');
$canChange  = $user->authorise('core.edit.state', 'com_yoursites');

?>
<td class="sitename">
	<?php

	if (isset($item->isup) && !$item->isup) :
	$tooltip = ' data-yspoptitle = "' . \JText::_('COM_YOURSITES_SITE_INACCESSIBLE', true) . '"'
		. '  data-yspopcontent = "' . \JText::sprintf("COM_YOURSITES_SITE_APPEARS_TO_BE_INACCESSIBLE_CLICK_TO_CHECK_AGAIN", $item->siteurl, true) . '" '
		. ' data-yspopoptions = \'{"mode" : "hover", "offset" : 20,"delayHide" : 200, "pos" : "top"}\'';
	?>
    <div class="site-down">
        <a class="sitenotup"
           href="#progressModal"
           onclick="document.adminForm['cb<?php echo $i; ?>'].checked = true;checksiteup();return false;"
        >
                                        <span class="icon-warning hasYsPopover"
                                              aria-hidden="true" <?php echo $tooltip; ?> ></span>
        </a>
		<?php elseif (isset($item->isup) && $item->isup == 2) :
		$tooltip = ' data-yspoptitle = "' . \JText::_('COM_YOURSITES_SITE_OFFLINE', true) . '"'
			. '  data-yspopcontent = "' . \JText::sprintf("COM_YOURSITES_SITE_APPEARS_TO_BE_OFFLINE_CLICK_TO_CHECK_AGAIN", $item->siteurl, true) . '" '
			. ' data-yspopoptions = \'{"mode" : "hover", "offset" : 20,"delayHide" : 200, "pos" : "top"}\'';
		?>
        <a class="siteofline"
           href="#progressModal"
           onclick="document.adminForm['cb<?php echo $i; ?>'].checked = true;checksiteup();return false;"
        >
                                        <span class="icon-ban-circle hasYsPopover"
                                              aria-hidden="true" <?php echo $tooltip; ?> ></span>
        </a>
        <div class="site-offline">
			<?php endif; ?>
			<?php if (isset($item->checked_out) && $item->checked_out && ($canEdit || $canChange)) : ?>
				<?php echo JHtml::_('jgrid.checkedout', $i, $item->uEditor, $item->checked_out_time, 'sites.', $canCheckin); ?>
			<?php endif; ?>
			<?php if ($canEdit) : ?>
                <a href="<?php echo JRoute::_('index.php?option=com_yoursites&task=site.edit&id=' . (int) $item->id); ?>"
                   id="sitename<?php echo $item->id; ?>">
					<?php echo $this->escape($item->sitename); ?>
                </a>
			<?php else : ?>
                <span id="sitename<?php echo $item->id; ?>">
                                        <?php echo $this->escape($item->sitename); ?>
                                    </span>
			<?php endif; ?>
            <br/>
            <a href="<?php echo $this->escape($item->siteurl); ?>" id="siteurl<?php echo $item->id; ?>"
               target="_blank">
				<?php if (!$secureUrl) {
				$tooltip = ' data-yspoptitle = "' . \JText::_('COM_YOURSITES_SITE_INSECURE', true) . '"'
					. '  data-yspopcontent = "' . JText::_("COM_YOURSITES_SITE_APPEARS_TO_HAVE_INSECURE_CONNECTION", true) . '" '
					. ' data-yspopoptions = \'{"mode" : "hover", "offset" : 20,"delayHide" : 200, "pos" : "top"}\'';
				?>
                <span class="siteurlnotsecure">
                    <span class="icon-unlock hasYsPopover sitenotsecure"
                          aria-hidden="true" <?php echo $tooltip; ?> >
                    </span>
                <?php }
                else if(isset($item->advisorydata) && !empty($item->advisorydata->certexpires)) {
                    $tooltip = '  data-yspoptitle = "' . \JText::_('COM_YOURSITES_SITE_CERTIFICATE_EXPIRATION', true)
                        . '"  data-yspopcontent = "' . \JText::sprintf("COM_YOURSITES_ADVCHECK_CHECKCERTIFICATE_INFO", $item->advisorydata->certexpires, true) . '" '
	                    . ' data-yspopoptions = \'{"mode" : "hover", "offset" : 20,"delayHide" : 200, "pos" : "top"}\'';
                    ;
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

                if (strpos($item->sitename, $siteurl) === 0 || strpos($siteurl, $item->sitename ) === 0 )
                {
	                echo "{" . $this->escape($siteurl) . "}";
                }
                else
                {
	                echo $this->escape($siteurl);
                }
                ?>
                <?php if (!$secureUrl) : ?>
                </span>
			<?php endif; ?>
            </a>
			<?php if (isset($item->isup) && !$item->isup) : ?>
        </div>
	<?php endif; ?>

        <input type="hidden" id="coretype<?php echo $i; ?>" value="<?php echo $item->coretype; ?>"/>
</td>

