<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: cpanel.php 3119 2011-12-20 14:34:33Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\Helpers\Sidebar;

JEventsHelper::addSubmenu();
$this->sidebar = Sidebar::render();

$mainspan = 10;
$fullspan = 12;
?>
<?php if (!empty($this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
<?php endif; ?>
<div id="jevents">
	<form action="index.php" method="post" name="adminForm" id="adminForm">
		<div id="j-main-container" class="span<?php echo (!empty($this->sidebar)) ? $mainspan : $fullspan; ?>  ">
			<table border="0" cellpadding="2" cellspacing="2" class="adminform">

				<tr>
					<td width="50%" valign="top">
						<div id="cpanel">
							<?php
							$clubnews = $this->renderVersionsForClipboard();
							$label    = Text::_("JEV_VERSION_INFORMATION_FOR_SUPPORT");
							?>
							<div style="width: 100%;padding:0px;">
								<strong><?php echo Text::_("JEV_VERSION_INFORMATION_FOR_SUPPORT_DESCRIPTION"); ?></strong>
								<br/>
								<?php echo $clubnews; ?>
							</div>
						</div>
					</td>
					<td width="50%" valign="top">

					</td>
				</tr>
			</table>
			<p align="center">
				<a href="<?php $version = JEventsVersion::getInstance();
				echo $version->getUrl();
				?>" target="_blank" style="font-size:xx-small;"
				   title="Events Website"><?php echo $version->getLongVersion(); ?></a>
				&nbsp;
				<span style="color:#999999; font-size:9px;"><?php echo $version->getShortCopyright(); ?></span>
			</p>

			<input type="hidden" name="task" value="cpanel"/>
			<input type="hidden" name="act" value=""/>
			<input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT; ?>"/>
		</div>
	</form>
</div>
