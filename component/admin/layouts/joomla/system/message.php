<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

// dummy output which we are hijacking to place withing our scope!

$msgList = $displayData['msgList'];
$jversion = new Version;

// Skip Chosen in Joomla 4.x+
if ($jversion->isCompatible('4.0'))
{
	include (JPATH_SITE . "/layouts/joomla/system/message.php");

}
else
{
$alert = array('error' => 'alert-error', 'warning' => '', 'notice' => 'alert-info', 'message' => 'alert-success');
ob_start();
?>
<div id="system-message-container">
	<?php if (is_array($msgList) && !empty($msgList)) : ?>
		<div id="system-message">
			<?php foreach ($msgList as $type => $msgs) : ?>
				<div class="alert <?php echo isset($alert[$type]) ? $alert[$type] : 'alert-' . $type; ?>">
					<?php // This requires JS so we should add it through JS. Progressive enhancement and stuff. ?>
					<a class="close" data-dismiss="alert">×</a>

					<?php if (!empty($msgs)) : ?>
						<h4 class="alert-heading"><?php echo Text::_($type); ?></h4>
						<div>
							<?php foreach ($msgs as $msg) : ?>
								<div class="alert-message"><?php echo $msg; ?></div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
<?php
}
$messages = json_encode(ob_get_clean());
?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('ysts_system_messages').innerHTML = <?php echo $messages;?>;
    });
</script>
