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

$msgList  = $displayData['msgList'];
$jversion = new Joomla\CMS\Version;

// Skip Chosen in Joomla 4.x+
ob_start();
if ($jversion->isCompatible('4.0'))
{
	include(JPATH_SITE . "/layouts/joomla/system/message.php");

}
else
{
	$alert = array('error' => 'alert-error', 'warning' => '', 'notice' => 'alert-info', 'message' => 'alert-success');
	?>
	<div id="system-message-container">
		<?php if (is_array($msgList) && !empty($msgList)) : ?>
			<div id="system-message">
				<?php foreach ($msgList as $type => $msgs) : ?>
					<div class="alert <?php echo isset($alert[$type]) ? $alert[$type] : 'alert-' . $type; ?>">
						<?php // This requires JS so we should add it through JS. Progressive enhancement and stuff. ?>
						<a class="close" data-dismiss="alert">×</a>

						<?php if (!empty($msgs)) : ?>
							<h4 class="alert-heading"><?php echo JText::_($type); ?></h4>
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
$messages = ob_get_clean();
if (Factory::getApplication()->isClient('administrator'))
{
	$document = Factory::getDocument();
	$buffer   = $document->getBuffer('component');

	$buffer = str_replace("YSTS_SYSTEM_MESSAGES", $messages, $buffer);
	$document->setBuffer($buffer, array('type' => 'component', 'name' => null, 'title' => null));
}
else
{
	echo $messages;
}
