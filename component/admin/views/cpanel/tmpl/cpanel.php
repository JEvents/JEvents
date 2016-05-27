<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: cpanel.php 3119 2011-12-20 14:34:33Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2016 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');
$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
$version = JEventsVersion::getInstance();

JEVHelper::stylesheet('jev_cp.css', 'administrator/components/' . JEV_COM_COMPONENT . '/assets/css/');

if (!empty($this->sidebar))
{
	?>
	<div id="j-sidebar-container" class="span2">

		<?php echo $this->sidebar; ?>

		<?php
		//Version Checking etc
		?>
		<div class="jev_version">
			<?php echo JText::sprintf('JEV_CURRENT_VERSION', JString::substr($version->getShortVersion(), 1)); ?>
		</div>
	</div>
<?php
}
$mainspan = 10;
$fullspan = 12;
?>

<div id="jevents" class="span12">
    <form action="index.php" method="post" name="adminForm" id="adminForm">
        <div id="j-main-container" class="span<?php echo (!empty($this->sidebar)) ? $mainspan : $fullspan; ?>  ">
            <div id="cpanel" class="well well-small clearfix ">
				<?php
				if (JEVHelper::isAdminUser())
				{
					$link = "index.php?option=" . JEV_COM_COMPONENT . "&task=icals.list";
					$this->_quickiconButtonWHover($link, "cpanel/CalendarsCool.png", "cpanel/CalendarsHot.png", JText::_('JEV_ADMIN_ICAL_SUBSCRIPTIONS'), "/administrator/components/" . JEV_COM_COMPONENT . "/assets/images/");
				}

				$link = "index.php?option=" . JEV_COM_COMPONENT . "&task=icalevent.list";
				$this->_quickiconButtonWHover($link, "cpanel/EventsCool.png", "cpanel/EventsHot.png", JText::_('JEV_ADMIN_ICAL_EVENTS'), "/administrator/components/" . JEV_COM_COMPONENT . "/assets/images/");

				$link = "index.php?option=com_categories&extension=" . JEV_COM_COMPONENT;

				$this->_quickiconButtonWHover($link, "cpanel/CategoriesCool.png", "cpanel/CategoriesHot.png", JText::_('JEV_INSTAL_CATS'), "/administrator/components/" . JEV_COM_COMPONENT . "/assets/images/");

				if (JEVHelper::isAdminUser())
				{
                                    if ($params->get("authorisedonly", 0)) {
					$link = "index.php?option=" . JEV_COM_COMPONENT . "&task=user.list";
					$this->_quickiconButtonWHover($link, "cpanel/AuthorisedCool.png", "cpanel/AuthorisedHot.png", JText::_('JEV_MANAGE_USERS'), "/administrator/components/" . JEV_COM_COMPONENT . "/assets/images/");
                                    }
					// new version - Joomla 3.5 does its stuff using AJAX and assumes its ONLY called from com_config :(
					$link = "index.php?option=" . JEV_COM_COMPONENT . "&task=params.edit&view=component&component=com_jevents";
					$this->_quickiconButtonWHover($link, "cpanel/ConfigCool.png", "cpanel/ConfigHot.png", JText::_('JEV_INSTAL_CONFIG'), "/administrator/components/" . JEV_COM_COMPONENT . "/assets/images/");
				}
				if (JEVHelper::isAdminUser())
				{
					$link = "index.php?option=" . JEV_COM_COMPONENT . "&task=defaults.list";
					$this->_quickiconButtonWHover($link, "cpanel/LayoutsCool.png", "cpanel/LayoutsHot.png", JText::_('JEV_LAYOUT_DEFAULTS'), "/administrator/components/" . JEV_COM_COMPONENT . "/assets/images/");
					// Custom CSS
					$link = "index.php?option=" . JEV_COM_COMPONENT . "&task=cpanel.custom_css";
					$this->_quickiconButtonWHover($link, "cpanel/CSSCool.png", "cpanel/CSSHot.png", JText::_('JEV_CUSTOM_CSS'), "/administrator/components/" . JEV_COM_COMPONENT . "/assets/images/");
					// Support Info
					$link = "index.php?option=" . JEV_COM_COMPONENT . "&task=cpanel.support";
					$this->_quickiconButtonWHover($link, "cpanel/SupportCool.png", "cpanel/SupportHot.png", JText::_('SUPPORT_INFO'), "/administrator/components/" . JEV_COM_COMPONENT . "/assets/images/");
					//Manage Addons
					//$link = "index.php?option=" . JEV_COM_COMPONENT . "&task=cpanel.addons";
					//$this->_quickiconButtonWHover($link, "cpanel/JEventsAddonsCool.png", "cpanel/JEventsAddonsHot.png", JText::_('JEV_MANAGE_ADDONS'), "/administrator/components/" . JEV_COM_COMPONENT . "/assets/images/", '_blank');
					//Project News
					$link = "https://www.jevents.net/news";
					$this->_quickiconButtonWHover($link, "cpanel/NewsCool.png", "cpanel/NewsHot.png", JText::_('JEV_NEWS'), "/administrator/components/" . JEV_COM_COMPONENT . "/assets/images/", '_blank');
				}
				// Links to addons
				// Managed Locations
				$db = JFactory::getDbo ();
				$db->setQuery ( "SELECT enabled FROM #__extensions WHERE element = 'com_jevlocations' AND type='component' " );
				$is_enabled = $db->loadResult ();
				if ($is_enabled) {
					$link = "index.php?option=com_jevlocations";
					JFactory::getLanguage()->load("com_jevlocations", JPATH_ADMINISTRATOR);
					$this->_quickiconButtonWHover($link, "cpanel/LocationsCool.png", "cpanel/LocationsHot.png", JText::_('COM_JEVLOCATIONS'), "/administrator/components/" . JEV_COM_COMPONENT . "/assets/images/");
				}
				// Managed People
				$db = JFactory::getDbo ();
				$db->setQuery ( "SELECT enabled FROM #__extensions WHERE element = 'com_jevpeople' AND type='component' " );
				$is_enabled = $db->loadResult ();
				if ($is_enabled) {
					$link = "index.php?option=com_jevpeople";
					JFactory::getLanguage()->load("com_jevpeople", JPATH_ADMINISTRATOR);
					$this->_quickiconButtonWHover($link, "cpanel/PeopleCool.png", "cpanel/PeopleHot.png", JText::_('COM_JEVPEOPLE'), "/administrator/components/" . JEV_COM_COMPONENT . "/assets/images/");
				}
				// RSVP Pro
				$db = JFactory::getDbo ();
				$db->setQuery ( "SELECT enabled FROM #__extensions WHERE element = 'com_rsvppro' AND type='component' " );
				$is_enabled = $db->loadResult ();
				if ($is_enabled) {
					$link = "index.php?option=com_rsvppro";
					JFactory::getLanguage()->load("com_rsvppro", JPATH_ADMINISTRATOR);
					$this->_quickiconButtonWHover($link, "cpanel/RSVPCool.png", "cpanel/RSVPHot.png", JText::_('COM_RSVPPRO'), "/administrator/components/" . JEV_COM_COMPONENT . "/assets/images/");
				}
				// Custom Fields				
				$db = JFactory::getDbo ();
				$db->setQuery ( "SELECT * FROM #__extensions WHERE element = 'jevcustomfields' AND type='plugin' AND folder='jevents' " );
				$extension = $db->loadObject();
				// Stop if user is not authorised to manage JEvents
				if ($extension && $extension->enabled && JEVHelper::isAdminUser()) {
					$manifestCache = json_decode($extension->manifest_cache);
					if (version_compare($manifestCache->version, "3.5.0RC", "ge") )
					{
						$link = "index.php?option=com_jevents&task=plugin.jev_customfields.overview";
						JFactory::getLanguage()->load("plg_jevents_jevcustomfields", JPATH_ADMINISTRATOR);
						$this->_quickiconButtonWHover($link, "cpanel/CustomFieldsCool.png", "cpanel/CustomFieldsHot.png", JText::_('JEV_CUSTOM_FIELDS'), "/administrator/components/" . JEV_COM_COMPONENT . "/assets/images/");
					}
				}

				?>
                <div class="clear"></div>
            </div>
        </div>
		<?php
		if (JText::_("JEV_TRANSLATION_CREDITS") != "JEV_TRANSLATION_CREDITS" && JFactory::getLanguage()->getTag() != "en-GB")
		{
			?>
			<div class="span12 center">
				<strong><?php echo JText::_("JEV_TRANSLATION_CREDITS"); ?>:</strong>
				<i><?php echo JText::_("JEV_TRANSLATION_LANGUAGE"); ?></i> - <?php echo $this->getTranslatorLink(); ?>
			</div>
			<?php
		}
		?>
        <div class="span12 center">
            <a href="<?php
			   echo $version->getUrl();
			   ?>" target="_blank" style="font-size:xx-small;"
               title="Events Website"><?php echo $version->getLongVersion(); ?></a>
            &nbsp;
            <span style="color:#999999; font-size:9px;"><?php echo $version->getShortCopyright(); ?></span>
        </div>

        <input type="hidden" name="task" value="cpanel"/>
        <input type="hidden" name="act" value=""/>
        <input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT; ?>"/>
    </form>
</div>
