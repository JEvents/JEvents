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
$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
?>
<div id="jevents" class="span12">
	<?php
	if (isset($this->warning))
	{
		?>
		<dl id="system-message">
			<dt class="notice">Message</dt>
			<dd class="notice">
				<ul>
					<li><?php echo $this->warning; ?></li>
				</ul>
			</dd>
		</dl>   	
		<?php
	}
	?>
	<form action="index.php" method="post" name="adminForm" id="adminForm">
		<?php if (!empty($this->sidebar))
		{
			?>
			<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
			</div>
			<?php }
			if ($params->get("showPanelNews", 1) == 1){
				$mainspan = 5;
				$fullspan = 7;
			} else {
				$mainspan = 10;
				$fullspan = 12;
			}
			
		?>
		<div id="j-main-container" class="span<?php echo (!empty($this->sidebar)) ? $mainspan : $fullspan; ?>  ">
			<div id="cpanel" class="well well-small clearfix ">
				<?php
				if (JEVHelper::isAdminUser())
				{
					$link = "index.php?option=" . JEV_COM_COMPONENT . "&task=icals.list";
					$this->_quickiconButton($link, "jevents_calendar_sml.png", JText::_('JEV_ADMIN_ICAL_SUBSCRIPTIONS'), "/administrator/components/" . JEV_COM_COMPONENT . "/assets/images/");
				}

				$link = "index.php?option=" . JEV_COM_COMPONENT . "&task=icalevent.list";
				$this->_quickiconButton($link, "jevents_event_sml.png", JText::_('JEV_ADMIN_ICAL_EVENTS'), "/administrator/components/" . JEV_COM_COMPONENT . "/assets/images/");

				$link = "index.php?option=com_categories&extension=" . JEV_COM_COMPONENT;

				$this->_quickiconButton($link, "jevents_categories_sml.png", JText::_('JEV_INSTAL_CATS'), "/administrator/components/" . JEV_COM_COMPONENT . "/assets/images/");

				if (JEVHelper::isAdminUser())
				{
					$link = "index.php?option=" . JEV_COM_COMPONENT . "&task=user.list";
					$this->_quickiconButton($link, "jevents_user_sml.png", JText::_('JEV_MANAGE_USERS'), "/administrator/components/" . JEV_COM_COMPONENT . "/assets/images/");

					// new version
					$link = "index.php?option=" . JEV_COM_COMPONENT . "&task=params.edit";
					$this->_quickiconButton($link, "jevents_config_sml.png", JText::_('JEV_INSTAL_CONFIG'), "/administrator/components/" . JEV_COM_COMPONENT . "/assets/images/");
				}
				if (JEVHelper::isAdminUser())
				{
					$link = "index.php?option=" . JEV_COM_COMPONENT . "&task=defaults.list";
					$this->_quickiconButton($link, "jevents_layouts_sml.png", JText::_('JEV_LAYOUT_DEFAULTS'), "/administrator/components/" . JEV_COM_COMPONENT . "/assets/images/");
					// Custom CSS
					$link = "index.php?option=" . JEV_COM_COMPONENT . "&task=cpanel.custom_css";
					$this->_quickiconButton($link, "jevents_customcss_sml.png", JText::_('JEV_CUSTOM_CSS'), "/administrator/components/" . JEV_COM_COMPONENT . "/assets/images/");
					// Support Info
					$link = "index.php?option=" . JEV_COM_COMPONENT . "&task=cpanel.support";
					$this->_quickiconButton($link, "Support_icon.png", JText::_('SUPPORT_INFO'), "/administrator/components/" . JEV_COM_COMPONENT . "/assets/images/");
				}

				
				?>
				<div class="clear"></div>
			</div>
		</div>
		<div class="span5">
			<?php
			
			if ($params->get("showPanelNews", 1))
			{
				try {
					echo JHtml::_('sliders.start', 'cpanel-sliders');
					echo JHtml::_('sliders.panel', JText::_("JEV_News"), 'cpanelnews');
					?>
					<div class="well well-small ">
					<?php echo $this->renderJEventsNews(); ?>
					</div>
					<?php
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
						echo JHtml::_('sliders.panel', $label, 'cpanelstatus');
						?>
						<div  class="well well-small "  style="overflow:auto">
						<?php echo $clubnews; ?>
						</div> <?php
					}
					echo JHtml::_('sliders.end');
				}
				catch (Exception $exc) {
					echo $exc->getMessage();
				}

			}
			?>                                    
		</div>
		<?php
		if (JText::_("JEV_TRANSLATION_CREDITS") != "JEV_TRANSLATION_CREDITS" &&  JFactory::getLanguage()->getTag() != "en-GB")
		{
			?>
			<div class="span12 center">
				<strong><?php echo JText::_("JEV_TRANSLATION_CREDITS"); ?>:</strong> <i><?php echo JText::_("JEV_TRANSLATION_LANGUAGE"); ?></i> - <?php echo $this->getTranslatorLink(); ?>
			</div>
			<?php
		}
		?>
		<div class="span12 center">
			<a href="<?php
		$version = JEventsVersion::getInstance();
		echo $version->getUrl();
		?>" target="_blank" style="font-size:xx-small;" title="Events Website"><?php echo $version->getLongVersion(); ?></a>
			&nbsp;
			<span style="color:#999999; font-size:9px;"><?php echo $version->getShortCopyright(); ?></span>
		</div>

		<input type="hidden" name="task" value="cpanel" />
		<input type="hidden" name="act" value="" />
		<input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT; ?>" />
	</form>
</div>
