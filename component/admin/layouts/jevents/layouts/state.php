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

$user = JFactory::getUser();
$canCreate = $user->authorise('core.create', 'com_yoursites');
$canEdit = $user->authorise('core.edit', 'com_yoursites');
$canCheckin = $user->authorise('core.manage', 'com_yoursites');
$canChange = $user->authorise('core.edit.state', 'com_yoursites');

if (isset($firstitem->state)):
?>
<td class="gsl-text-center">
    <div class="btn-group">
		<?php echo JHtml::_('jgrid.published', $item->state, $i, 'sites.', $canChange, 'cb'); ?>
		<?php
		JHtml::_('actionsdropdown.' . ((int)$item->state === 2 ? 'un' : '') . 'archive', 'cb' . $i, 'sites');
		JHtml::_('actionsdropdown.' . ((int)$item->state === -2 ? 'un' : '') . 'trash', 'cb' . $i, 'sites');
		echo JHtml::_('actionsdropdown.render', $this->escape($item->sitename));
		?>
    </div>
</td>
<?php endif;
return;
?>

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

$user = JFactory::getUser();
$canCreate = $user->authorise('core.create', 'com_yoursites');
$canEdit = $user->authorise('core.edit', 'com_yoursites');
$canCheckin = $user->authorise('core.manage', 'com_yoursites');
$canChange = $user->authorise('core.edit.state', 'com_yoursites');

if (isset($firstitem->state)):
	?>
    <td class="gsl-text-center">
        <button class="gsl-button gsl-button-default gsl-button-small active hasYsTooltip"
                href="javascript:void(0);"
                onclick="return listItemTask('cb11','sites.unpublish')"
                title="Unpublish Item">
            <span gsl-icon="icon:check" aria-hidden="true"></span>
        </button>
		<?php
		JHtml::_('actionsdropdown.' . ((int)$item->state === 2 ? 'un' : '') . 'archive', 'cb' . $i, 'sites');
		JHtml::_('actionsdropdown.' . ((int)$item->state === -2 ? 'un' : '') . 'trash', 'cb' . $i, 'sites');
		echo JHtml::_('actionsdropdown.render', $this->escape($item->sitename));
		?>
    </td>
<?php endif; ?>
