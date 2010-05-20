<?php 
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id$
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access'); ?>
<div id="jevents">
   <?php if (isset($this->warning)){
   	?>
		<dl id="system-message">
		<dt class="notice">Message</dt>
		<dd class="notice fade">
			<ul>
				<li><?php echo $this->warning;?></li>
			</ul>
		</dd>
		</dl>   	
   	<?php
   }
   ?>
   <form action="index.php" method="post" name="adminForm" >
	<table width="90%" border="0" cellpadding="2" cellspacing="2" class="adminform">
	
		<tr>
			<td width="55%" valign="top">
				<div id="cpanel">
				<?php				

				$link = "index.php?option=$option&task=icals.list";
				$this->_quickiconButton( $link, "jevents_calendar_sml.png", JText::_('JEV_ADMIN_ICAL_SUBSCRIPTIONS') ,"/administrator/components/".JEV_COM_COMPONENT."/assets/images/");

				$link = "index.php?option=$option&task=icalevent.list";
				$this->_quickiconButton( $link, "jevents_event_sml.png", JText::_('JEV_ADMIN_ICAL_EVENTS')  ,"/administrator/components/".JEV_COM_COMPONENT."/assets/images/");

				$link = "index.php?option=$option&task=categories.list";
				$this->_quickiconButton( $link, "icon-48-category.png", JText::_('JEV_INSTAL_CATS')  ,"/administrator/templates/khepri/images/header/");

				$link = "index.php?option=$option&task=user.list";
				$this->_quickiconButton( $link, "icon-48-user.png", JText::_('JEV_MANAGE_USERS') ,"/administrator/templates/khepri/images/header/");
				
				//$link = "index.php?option=$option&task=config.edit";
				// new version
				$link = "index.php?option=$option&task=params.edit";
				$this->_quickiconButton( $link, "icon-48-config.png", JText::_('JEV_INSTAL_CONFIG') ,"/administrator/templates/khepri/images/header/");

				$link = "index.php?option=$option&task=defaults.list";
				$this->_quickiconButton( $link, "icon-48-article.png", JText::_('JEV_LAYOUT_DEFAULTS') ,"/administrator/templates/khepri/images/header/");
				
				$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
				if ($this->migrated && !$params->get("hideMigration",0)){
					$link = "index.php?option=$option&task=config.convert";				
					$this->_quickiconButton( $link, "dbrestore.png", JText::_('JEV_ADMIN_CONVERT'),"","","if (!confirm('".JText::_("Are you sure?")."')) return false;");
				}				
				?>
				
				</div>
			</td>
			<td width="45%" valign="top">
				<?php
				$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
				if ($params->get("showPanelNews",1)) {
				?>
				<div style="width: 100%;">
					<table class="adminlist">
						<tr class="row0">
							<td><?php echo $this->renderJEventsNews();?></td>
						</tr>
					</table>
				</div>
				<?php } ?>
			</td>
		</tr>
  </table>
 <p align="center">
	<a href="<?php $version = & JEventsVersion::getInstance();echo $version->getUrl();?>" target="_blank" style="font-size:xx-small;" title="Events Website"><?php echo $version->getLongVersion();?></a>
			&nbsp;
			<span style="color:#999999; font-size:9px;"><?php echo $version->getShortCopyright();?></span>
</p>
 
  <input type="hidden" name="task" value="cpanel" />
  <input type="hidden" name="act" value="" />
  <input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT; ?>" />
</form>
</div>
