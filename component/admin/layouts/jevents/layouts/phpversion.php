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
<td class="phpversion">
    <span id="phpversion<?php echo $item->id; ?>">
        <?php echo $item->phpversion;?>
    </span>
</td>

