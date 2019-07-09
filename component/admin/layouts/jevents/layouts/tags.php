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
<?php if ($showTags) : ?>
    <td>
		<?php
		if (count($item->tags->itemTags) && $item->tags->tagLayout = new JLayoutFile('joomla.content.tags')) {

			echo $item->tags->tagLayout->render($item->tags->itemTags);
		}
		?>
    </td>
<?php endif; ?>
