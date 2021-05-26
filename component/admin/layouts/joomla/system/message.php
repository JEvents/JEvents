<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;

// dummy output which we are hijacking to place withing our scope!

$msgList = $displayData['msgList'];
$jversion = new Joomla\CMS\Version;

ob_start();
if ($jversion->isCompatible('4.0'))
{
	include (JPATH_SITE . "/layouts/joomla/system/message.php");
}
else
{
	$alert = array('error' => 'danger', 'warning' => 'warning', 'notice' => 'primary', 'message' => 'success');
	?>
	<div id="system-message-container">
		<?php if (is_array($msgList) && !empty($msgList)) : ?>
			<div id="system-message">
				<?php foreach ($msgList as $type => $msgs) : ?>
					<div class="gsl-alert gsl-alert-<?php echo isset($alert[$type]) ? $alert[$type] : $type; ?>" data-gsl-alert>
						<?php // This requires JS so we should add it through JS. Progressive enhancement and stuff. ?>
						<a class="gsl-alert-close" data-gsl-close></a>

						<?php if (!empty($msgs)) : ?>
							<h4 class="gsl-text-leading"><?php echo JText::_($type); ?></h4>
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
        var ysts_system_messages = document.getElementById('ysts_system_messages');
        if (ysts_system_messages) {
            ysts_system_messages.innerHTML = <?php echo $messages;?>;
        }
    });
</script>