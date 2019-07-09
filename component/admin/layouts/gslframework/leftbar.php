<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

use \Joomla\CMS\Factory;

// Joomla form submission scripts etc.
JHtml::_('behavior.core');

$app    = Factory::getApplication();
$input  = $app->input;
$task   = $input->getCmd('jevtask', 'icalevent.list');
list($view, $layout) = explode(".", $task);

$params = JComponentHelper::getParams("com_jevents");

$activeClass = 'class="gsl-active"';

JFactory::getDocument()->addScriptDeclaration('ys_popover(".hasYsPopover");');

?>
<aside id="left-col" class="gsl-padding-remove  gsl-background-secondary hide-label ">

	<?php
	$sitelayouts    = array();
	$extensionlayouts    = array();
	?>

    <nav class="left-nav-wrap  gsl-width-auto@m gsl-navbar-container" gsl-navbar>
        <div class="left-logo gsl-background-secondary"  gsl-toggle="target:#left-col, #left-col .left-nav, .ysts-page-title; mode: hover;cls: hide-label">
            <div>
                <a href="<?php echo JRoute::_("index.php?option=com_jevents&view=cpanel"); ?>" class="">
                    <img src="<?php echo JUri::base(); ?>components/com_jevents/assets/images/logo.png"
                         alt="JEvents Logo">
                    <span class="nav-label"><?php echo JText::_('JEVENTS_CORE_CPANEL'); ?></span>
                </a>
            </div>

        </div>

        <div class="gsl-navbar gsl-background-secondary"  >
            <ul class="left-nav gsl-navbar-nav gsl-list hide-label gsl-background-secondary" gsl-toggle="target:#left-col, #left-col .left-nav, .ysts-page-title; mode: hover;cls: hide-label">
                <?php
                if (JEVHelper::isAdminUser())
                {
	                ?>
                    <li <?php if ($view == "icals") { ?> class="gsl-active" <?php } ?> >
                        <a href="<?php echo JRoute::_("index.php?option=com_jevents&task=icals.list"); ?>" class=""
                        >
                            <span data-gsl-icon="icon: thumbnails"
                                  class="gsl-margin-small-right gsl-display-inline-block"></span>
                            <span class="nav-label"><?php echo JText::_('JEV_ADMIN_ICAL_SUBSCRIPTIONS'); ?></span>
                        </a>
                    </li>
	                <?php
                }
	            ?>
                <li <?php if ($view == "icalevent") { ?> class="gsl-active" <?php } ?> >
                    <a href="<?php echo JRoute::_("index.php?option=com_jevents&task=icalevent.list"); ?>" class=""
                       >
                        <span data-gsl-icon="icon: calendar" class="gsl-margin-small-right"></span>
                        <span class="nav-label"><?php echo JText::_('JEV_ADMIN_ICAL_EVENTS'); ?></span>
                    </a>
                </li>
                <li <?php if ($view == "categories") { ?> class="gsl-active" <?php } ?> >
                    <a href="<?php echo JRoute::_("index.php?option=com_categories&extension=com_jevents"); ?>" class=""
                    >
                        <span data-gsl-icon="icon: album" class="gsl-margin-small-right"></span>
                        <span class="nav-label"><?php echo JText::_('JEV_INSTAL_CATS'); ?></span>
                    </a>
                </li>
                <?php
                if (JEVHelper::isAdminUser())
                {
	                if ($params->get("authorisedonly", 0))
	                {
		                ?>
                        <li <?php if ($view == "user") { ?> class="gsl-active" <?php } ?> >
                            <a href="<?php echo JRoute::_("index.php?option=com_jevents&task=user.list"); ?>" class=""
                            >
                                <span data-gsl-icon="icon: users" class="gsl-margin-small-right"></span>
                                <span class="nav-label"><?php echo JText::_('JEV_MANAGE_USERS'); ?></span>
                            </a>
                        </li>
		                <?php
	                }
                }
                ?>
                <li <?php if ($view == "defaults") { ?> class="gsl-active" <?php } ?> >
                    <a href="<?php echo JRoute::_("index.php?option=com_jevents&task=defaults.list"); ?>" class=""
                    >
                        <span data-gsl-icon="icon: file-edit" class="gsl-margin-small-right"></span>
                        <span class="nav-label"><?php echo JText::_('JEV_LAYOUT_DEFAULTS'); ?></span>
                    </a>
                </li>
                <li <?php if ($view == "customcss") { ?> class="gsl-active" <?php } ?> >
                    <a href="<?php echo JRoute::_("index.php?option=com_jevents&view=customcss"); ?>" class=""
                    >
                        <span data-gsl-icon="icon: paint-bucket" class="gsl-margin-small-right"></span>
                        <span class="nav-label"><?php echo JText::_('JEV_CUSTOM_CSS'); ?></span>
                    </a>
                </li>

                <?php
                // Links to addons
                // Managed Locations
                $db = Factory::getDbo();
                $db->setQuery("SELECT enabled FROM #__extensions WHERE element = 'com_jevlocations' AND type='component' ");
                $is_enabled = $db->loadResult();
                if ($is_enabled)
                {
	                Factory::getLanguage()->load("com_jevlocations", JPATH_ADMINISTRATOR);
	                ?>
                    <li <?php if ($view == "jevlocations") { ?> class="gsl-active" <?php } ?> >
                        <a href="<?php echo JRoute::_("index.php?option=com_jevlocations"); ?>" class=""
                        >
                            <span data-gsl-icon="icon: world" class="gsl-margin-small-right"></span>
                            <span class="nav-label"><?php echo JText::_('COM_JEVLOCATIONS'); ?></span>
                        </a>
                    </li>
                    <?php
                }
                // Managed People
                $db = Factory::getDbo();
                $db->setQuery("SELECT enabled FROM #__extensions WHERE element = 'com_jevpeople' AND type='component' ");
                $is_enabled = $db->loadResult();
                if ($is_enabled)
                {
	                Factory::getLanguage()->load("com_jevpeople", JPATH_ADMINISTRATOR);
	                ?>
                    <li <?php if ($view == "jevpeople") { ?> class="gsl-active" <?php } ?> >
                        <a href="<?php echo JRoute::_("index.php?option=com_jevpeople"); ?>" class=""
                        >
                            <span data-gsl-icon="icon: nut" class="gsl-margin-small-right"></span>
                            <span class="nav-label"><?php echo JText::_('COM_JEVPEOPLE'); ?></span>
                        </a>
                    </li>
	                <?php
                }
                // RSVP Pro
                $db = Factory::getDbo();
                $db->setQuery("SELECT enabled FROM #__extensions WHERE element = 'com_rsvppro' AND type='component' ");
                $is_enabled = $db->loadResult();
                if ($is_enabled)
                {
	                Factory::getLanguage()->load("com_rsvppro", JPATH_ADMINISTRATOR);
	                ?>
                    <li <?php if ($view == "rsvppro") { ?> class="gsl-active" <?php } ?> >
                        <a href="<?php echo JRoute::_("index.php?option=com_rsvppro"); ?>" class=""
                        >
                            <span data-gsl-icon="icon: cart" class="gsl-margin-small-right"></span>
                            <span class="nav-label"><?php echo JText::_('COM_RSVPPRO'); ?></span>
                        </a>
                    </li>
	                <?php
                }

                // Custom Fields
                $db = Factory::getDbo();
                $db->setQuery("SELECT * FROM #__extensions WHERE element = 'jevcustomfields' AND type='plugin' AND folder='jevents' ");
                $extension = $db->loadObject();
                // Stop if user is not authorised to manage JEvents
                if ($extension && $extension->enabled && JEVHelper::isAdminUser())
                {
	                ?>
                    <li>
                        <a href="<?php echo JRoute::_("index.php?option=com_jevents&task=plugin.jev_customfields.overview"); ?>">
                            <span data-gsl-icon="icon: code" class="gsl-margin-small-right"></span>
                            <span class="nav-label"><?php echo JText::_('JEV_CUSTOM_FIELDS'); ?></span>
                        </a>
                    </li>
	                <?php
                }
                ?>
            </ul>
        </div>
    </nav>
</aside>