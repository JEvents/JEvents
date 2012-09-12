<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: cpanel.php 3119 2011-12-20 14:34:33Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');
?>
<div id="jevents">
	<?php if (isset($this->warning))
	{ ?>
		<dl id="system-message">
			<dt class="notice">Message</dt>
			<dd class="notice fade">
				<ul>
					<li><?php echo $this->warning; ?></li>
				</ul>
			</dd>
		</dl>   	
		<?php
	}
	?>
	<form action="index.php" method="post" name="adminForm" id="adminForm">
		<table width="90%" border="0" cellpadding="2" cellspacing="2" class="adminform">

			<tr>
				<td width="55%" valign="top">
					<div id="cpanel">
						<?php
						if (JEVHelper::isAdminUser()){
							$link = "index.php?option=" . JEV_COM_COMPONENT . "&task=icals.list";
							$this->_quickiconButton($link, "jevents_calendar_sml.png", JText::_('JEV_ADMIN_ICAL_SUBSCRIPTIONS'), "/administrator/components/" . JEV_COM_COMPONENT . "/assets/images/");
						}

						$link = "index.php?option=" . JEV_COM_COMPONENT . "&task=icalevent.list";
						$this->_quickiconButton($link, "jevents_event_sml.png", JText::_('JEV_ADMIN_ICAL_EVENTS'), "/administrator/components/" . JEV_COM_COMPONENT . "/assets/images/");

						if (JVersion::isCompatible("1.6.0"))
						{
							$link = "index.php?option=com_categories&extension=" . JEV_COM_COMPONENT;
						}
						else
						{
							$link = "index.php?option=" . JEV_COM_COMPONENT . "&task=categories.list";
						}
						$this->_quickiconButton($link, "jevents_categories_sml.png", JText::_('JEV_INSTAL_CATS'), "/administrator/components/" . JEV_COM_COMPONENT . "/assets/images/");

						if (JEVHelper::isAdminUser())
						{
							$link = "index.php?option=" . JEV_COM_COMPONENT . "&task=user.list";
							$this->_quickiconButton($link, "jevents_user_sml.png", JText::_('JEV_MANAGE_USERS'), "/administrator/components/" . JEV_COM_COMPONENT . "/assets/images/");

							// new version
							$link = "index.php?option=" . JEV_COM_COMPONENT . "&task=params.edit";
							$this->_quickiconButton($link, "jevents_config_sml.png", JText::_('JEV_INSTAL_CONFIG'), "/administrator/components/" . JEV_COM_COMPONENT . "/assets/images/");

							$link = "index.php?option=" . JEV_COM_COMPONENT . "&task=defaults.list";
							$this->_quickiconButton($link, "jevents_layouts_sml.png", JText::_('JEV_LAYOUT_DEFAULTS'), "/administrator/components/" . JEV_COM_COMPONENT . "/assets/images/");
						}

						$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
						if ($this->migrated && !$params->get("hideMigration", 0))
						{
							$link = "index.php?option=" . JEV_COM_COMPONENT . "&task=config.convert";
							$this->_quickiconButton($link, "jevents_migrate_sml.png", JText::_('JEV_ADMIN_CONVERT'), "/administrator/components/" . JEV_COM_COMPONENT . "/assets/images/", "", "if (!confirm('" . JText::_('ARE_YOU_SURE') . "')) return false;");
						}
						?>

					</div>
				</td>
				<td width="45%" valign="top">
					<?php
					$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
					if ($params->get("showPanelNews", 1))
					{
						if (JVersion::isCompatible("1.6"))
						{
							echo JHtml::_('sliders.start', 'cpanel-sliders');
							echo JHtml::_('sliders.panel', JText::_("JEV_News"), 'cpanelnews');
						}
						else
						{
							$tabs = JPane::getInstance('tabs');
							echo $tabs->startPane('cpanel-tabs');
							echo $tabs->startPanel(JText::_("JEV_News"), 'cpanelstatus');
						}
						?>
						<div style="width: 100%;">
							<table class="adminlist">
								<tr class="row0">
									<td><?php echo $this->renderJEventsNews(); ?></td>
								</tr>
							</table>
						</div>
						<?php
						if (!JVersion::isCompatible("1.6"))
						{
							echo $tabs->endPanel();
						}
						$needsupdate = false;
						$clubnews = $this->renderVersionStatusReport($needsupdate);
						if ($needsupdate)
						{
							$label = JText::_("JEV_VERSION_STATUS_NEEDSUPDATE");
							$repid = 'updateavailable';
						}
						else
						{
							$label = JText::_("JEV_VERSION_STATUS_REPORT");
							$repid = 'statusreport';
						}
						if ($clubnews)
						{
							if (JVersion::isCompatible("1.6"))
							{
								echo JHtml::_('sliders.panel', $label, 'cpanelstatus');
							}
							else
							{
								echo $tabs->startPanel($label, 'cpanelstatus');
							}
							?>
							<div style="width: 100%;">
								<?php echo $clubnews; ?>
							</div> <?php
						if (!JVersion::isCompatible("1.6"))
						{
							echo $tabs->endPanel();
						}
					}
					if (!JVersion::isCompatible("1.6"))
					{
						echo $tabs->endPanel();
					}
					$needsupdate = false;
					$clubnews = $this->renderVersionsForClipboard();
					if ($clubnews)
					{
						$label = JText::_("JEV_VERSION_INFORMATION_FOR_SUPPORT");
						if (JVersion::isCompatible("1.6"))
						{
							echo JHtml::_('sliders.panel', $label, 'cpanelstatustextarea');
						}
						else
						{
							echo $tabs->startPanel($label, 'cpanelstatustextarea');
						}
								?>
							<div style="width: 100%;">
								<h3><?php echo JText::_("JEV_VERSION_INFORMATION_FOR_SUPPORT_DESCRIPTION");?></h3>
								<?php echo $clubnews; ?>
							</div> <?php
						if (!JVersion::isCompatible("1.6"))
						{
							echo $tabs->endPanel();
						}
					}
					if (JVersion::isCompatible("1.6"))
					{
						echo JHtml::_('sliders.end');
					}
					else
					{
						echo $tabs->endPane();
					}
				}
				?>
				</td>
			</tr>
		</table>
		<p align="center">
			<a href="<?php $version = & JEventsVersion::getInstance();
					echo $version->getUrl(); ?>" target="_blank" style="font-size:xx-small;" title="Events Website"><?php echo $version->getLongVersion(); ?></a>
			&nbsp;
			<span style="color:#999999; font-size:9px;"><?php echo $version->getShortCopyright(); ?></span>
		</p>

		<input type="hidden" name="task" value="cpanel" />
		<input type="hidden" name="act" value="" />
		<input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT; ?>" />
	</form>
</div>
