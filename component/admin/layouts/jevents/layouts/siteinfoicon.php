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
	if ($item->siteInfo && !empty($item->siteInfo)) {
		?>
        <div class="gsl-button ys-right-off-canvas ys-site-info-button toggleSiteInfo"
             data-toggleid="<?php echo $item->id; ?>">
            <a gsl-toggle="" href="#offcanvas-right-panel" class="hasYsPopover" title="Site Information">
                <span gsl-icon="icon: info" aria-hidden="true" style="min-height:20px;min-width: 20px;"></span>
            </a>
        </div>
		<?php
	}
	echo JLayoutHelper::render('yoursites.layouts.siteinfodata', array(
		"i" => $i, "item" => $item, "allowdirectlogin" => $allowdirectlogin));
	?>
</td>
