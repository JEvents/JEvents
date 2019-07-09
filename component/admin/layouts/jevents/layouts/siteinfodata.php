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
<div class="row<?php echo $i % 2; ?> siteinfo gsl-hidden item<?php echo $item->id; ?>">
    <table>
        <tr>
            <td colspan="<?php echo $allowdirectlogin ? 11 : 10; ?>">
                <div class="row-fluid siteinfo item<?php echo $item->id; ?>" gsl-grid>
                    <div class="gsl-width-1-2@s">
						<?php
						echo JLayoutHelper::render('yoursites.layouts.system', array(
							"i" => $i, "item" => $item
						));
						?>
                    </div>
                    <div class="gsl-width-1-2@s fieldinfo">
						<?php
						if (isset($item->jcfields))
						{
							echo JLayoutHelper::render('yoursites.layouts.fields', array(
								"item" => $item
							));
						}
						?>
						<?php

						echo JLayoutHelper::render('yoursites.layouts.sitestats', array(
							"item" => $item
						));

						echo JLayoutHelper::render('yoursites.layouts.tasks', array(
							"item" => $item
						));

						?>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</div>