<?php

use Joomla\CMS\Language\Text;

/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

$data = $displayData;

$title = htmlspecialchars(Text::_($data->tip ?: $data->title));

?>
<a href="#" onclick="return false;" class="js-stools-column-order hasYsPopover"
   data-order="<?php echo $data->order; ?>"
   data-direction="<?php echo strtoupper($data->direction); ?>"
   data-yspoptitle="<?php echo htmlspecialchars(Text::_($data->title)); ?>"
   data-name="<?php echo htmlspecialchars(Text::_($data->title)); ?>"
   title="<?php echo $title; ?>"
   data-yspopcontent="<?php echo htmlspecialchars(Text::_('JGLOBAL_CLICK_TO_SORT_THIS_COLUMN')); ?>"
   >
<?php if (!empty($data->icon)) : ?><span class="<?php echo $data->icon; ?>"></span><?php endif; ?>
<?php if (!empty($data->title)) : ?><?php echo Text::_($data->title); ?><?php endif; ?>
<?php if ($data->order == $data->selected) : ?><span class="<?php echo $data->orderIcon; ?>"></span><?php endif; ?>
</a>
