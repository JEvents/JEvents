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

$params = JComponentHelper::getParams("com_yoursites");
$allowdirectlogin = $params->get("allowdirectlogin", 1);
$allowinsecuredirectlogin = $params->get("allowinsecuredirectlogin", 0);

$loginuserid = intval($item->params->get("loginuserid", 0));
if ($loginuserid == -1)
{
	$loginuserid = 0;
}
$loginusername = $item->params->get("loginusername", '');
if ($item->coretype == 999)
{
	if ($allowdirectlogin) {
		?>
        <td/>
		<?php
	}
}
// Direct login is not offered for non https:// sites
else if ($allowdirectlogin) {
	?>
    <td>
		<?php

		// Userid of appropriate super/admin user
		if ($loginuserid > 0 || $loginuserid == -999 || !empty($loginusername)) {

			// Disable direct login on insecure URLs

			if (!$secureUrl && !$allowinsecuredirectlogin) {
				$tooltip = ' data-yspoptitle = "' . \JText::_('COM_YOURSITES_DIRECT_LOGIN_INSECURE', true). '"'
					. '  data-yspopcontent = "' . \JText::_("COM_YOURSITES_DIRECT_LOGIN_DISABLED", true) . '" '
					. ' data-yspopoptions = \'{"mode" : "hover", "offset" : 20,"delayHide" : 200, "pos" : "top"}\'';

				?>
                <div class="gsl-button gsl-button-small hasYsPopover gsl-button-danger" <?php echo $tooltip; ?>>
                    <span gsl-icon="icon:warning" aria-hidden="true"></span>
                </div>
				<?php
			} else {
				?>
                <a class="gsl-button gsl-button-small gsl-button-default"
                   href="javascript:void(0);"
                   onclick="return directlogin(<?php echo $i; ?>);"
                   title="<?php echo JText::_("COM_YOURSITES_DIRECT_LOGIN", false); ?>"
                >
                    <span gsl-icon="icon:sign-in" aria-hidden="true"></span>
                    <!--<span ><?php echo JText::_("COM_YOURSITES_DIRECT_LOGIN_LINK", false); ?></span>//-->
                </a>
				<?php
				// Should we show 2 factor authentication input field
				$twofatype = $item->has2fa ? 'text' : 'hidden';
				?>
                <input type="<?php echo $twofatype;?>" size="10" maxlength="255" id="twofa<?php echo $i;?>" class="twofainput"
                       onkeypress="if (event.keyCode == 13) return directlogin(<?php echo $i; ?>); else return true;"
                       placeholder="<?php echo JText::_("COM_YOURSITES_2_FACTOR_KEY", true);?>"/>
                <input type="hidden" class="has2fa<?php echo $item->id;?>" value="<?php echo $item->has2fa ? 1 : 0;?>" />
				<?php
			}
		} else {

		    // Todo color icon to login?
        }
		?>
    </td>
	<?php
} ?>
