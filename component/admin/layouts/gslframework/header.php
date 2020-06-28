<?php
/**
 * @version    CVS: JEVENTS_VERSION
 * @package    com_yoursites
 * @author     Geraint Edwards <yoursites@gwesystems.com>
 * @copyright  2016-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

// Do not do this in Internet Explorer 10 or lower (Note that MSIE 11 changed the app name to Trident)
if (GSLMSIE10)
{
    return;
}

use Joomla\CMS\Language\Text;
use Joomla\CMS\Version;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Factory;

// Skip Chosen in Joomla 4.x+
$jversion = new Version;
if ($jversion->isCompatible('4.0'))
{
	$document = Factory::getDocument();
	$document->addScriptDeclaration("j3 = false;");
}

// Load component specific data
$componentpath = dirname(dirname(dirname(__FILE__)));
include_once($componentpath . "/helpers/gslhelper.php");

GslHelper::loadAssets();
$app = Factory::getApplication();
$tmpl = $app->input->getCmd('tmpl', '');
?>
<div class="gsl-scope" id="gslc"> <!-- Open Custom UiKit Container -->
    <?php
    GslHelper::renderModal();
    ?>
    <div class="gsl-margin-remove gsl-grid" gsl-grid>
        <!-- LEFT BAR -->
		<?php
       if (empty($tmpl) && !$app->isClient('site'))
       {
	       echo LayoutHelper::render('gslframework.leftbar', null, dirname(__DIR__, 1));
       }
        ?>
        <!-- /LEFT BAR -->
        <div id="right-col" class="gsl-padding-remove gsl-width-expand@m <?php if (!empty($tmpl) || $app->isClient('site')) echo "noleftbar";?> ">

            <!--HEADER-->
            <header id="top-head" >
	            <?php
	            if (empty($tmpl) && !$app->isClient('site'))
	            {
	            ?>
                <nav class="gsl-navbar-container gsl-background-secondary ys-titlebar" gsl-navbar>
                    <div class="gsl-navbar-left gsl-background-secondary gsl-width-expand@m">
                        <?php
                        echo Factory::getApplication()->JComponentTitle;
                        ?>
                    </div>
                    <div class="gsl-navbar-right  gsl-background-secondary ">
                            <ul class="gsl-navbar-nav gsl-hidden">
	                            <?php
	                            $jversion = new JVersion;
	                            if (true || $jversion->isCompatible('4.0'))
	                            {
		                            ?>
                                    <li class="hasYsPopover ys_joomla"
                                        data-yspoptitle="<?php echo Text::_('COM_JEVENTS_RETURN_TO_JOOMLA', true); ?>"
                                        data-yspopcontent="<?php echo Text::_('COM_JEVENTS_RETURN_TO_JOOMLA_TOOLTIP', true); ?>"
                                    >
                                        <a href="<?php echo JURI::root() . 'index.php'; ?>"
                                           data-gsl-icon="icon: joomla"
                                           title="<?php Text::_('COM_JEVENTS_RETURN_TO_JOOMLA'); ?>"
                                           class="gsl-icon"
                                           aria-expanded="false">
                                        </a>
                                    </li>
		                            <?php
	                            }
	                            ?>
				                <?php
				                if (GslHelper::isAdminUser())
				                {
					                ?>
                                    <li class="hasYsPopover ys_support"
                                        data-yspoptitle="<?php echo GslHelper::translate('SUPPORT_FORUM'); ?>"
                                        data-yspopcontent="<?php echo GslHelper::translate('SUPPORT_FORUM_TOOLTIP'); ?>"
                                    >
                                        <a href="<?php echo GslHelper::supportLink(); ?>"
                                           data-gsl-icon="icon: question"
                                           title="<?php echo GslHelper::translate('SUPPORT_FORUM'); ?>"
                                           class="gsl-icon"
                                           target="_blank"
                                           aria-expanded="false">
                                        </a>
                                    </li>
                                    <li class="hasYsPopover  ys_docs"
                                        data-yspoptitle="<?php echo GslHelper::translate('DOCUMENTATION'); ?>"
                                        data-yspopcontent="<?php echo GslHelper::translate('DOCUMENTATION_TOOLTIP'); ?>"
                                    >
                                        <a href="<?php echo GslHelper::documentationLink(); ?>"
                                           data-gsl-icon="icon: file-text"
                                           title="<?php echo GslHelper::translate('DOCUMENTATION'); ?>"
                                           class="gsl-icon"
                                           target="_blank"
                                           aria-expanded="false">
                                        </a>
                                    </li>

                                    <li class="hasYsPopover  ys_config"
                                        data-yspoptitle="<?php echo GslHelper::translate('CONFIG'); ?>"
                                        data-yspopcontent="<?php echo GslHelper::translate('CONFIG_TOOLTIP'); ?>"
                                    >
                                        <a href="<?php echo GslHelper::configLink(); ?>"
                                           data-gsl-icon="icon: settings"
                                           title="<?php echo GslHelper::translate('CONFIG'); ?>"
                                           class="gsl-icon"
                                           aria-expanded="false">
                                        </a>
                                    </li>
	                            <?php
                            }
                            ?>
                            <li class="hasYsPopover  ys_logout"
                                data-yspoptitle = "<?php echo  Text::_('JLOGOUT', true); ?>"
                                data-yspopcontent = "<?php echo Text::_('JLOGOUT', true); ?>"
                            >
                                <a href="<?php echo Uri::base() . 'index.php?option=com_login&amp;task=logout&amp;' . Session::getFormToken() . '=1'; ?>"
                                   data-gsl-icon="icon:  sign-out"
                                   title="<?php Text::_('JLOGOUT'); ?>"
                                   aria-expanded="false">
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
		            <?php
	            }
	            ?>
                <nav class="gsl-navbar-container ys-gsl-action-buttons" gsl-navbar>
                    <div class="gsl-navbar-left gsl-background-primary gsl-width-expand@m">
                        <?php
                        $bar            = JToolBar::getInstance('toolbar2');
                        $toolbarButtons = $bar->getItems();

                        if (!count($toolbarButtons))
                        {
                            $bar            = JToolBar::getInstance('toolbar');
                            $toolbarButtons = $bar->getItems();
                        }

                        foreach ($toolbarButtons as $toolbarButton)
                        {
                            if (is_array($toolbarButton))
                            {
                                $buttonoutput = $bar->renderButton($toolbarButton);
                            }
                            else
                            {
                                // Joomla 4 in com_fields etc.
                                $buttonoutput = $toolbarButton->render();
                            }
                            $buttonoutput = str_replace("btn ", "gsl-button gsl-button-primary ", $buttonoutput);
                            $buttonoutput = str_replace('class=""', "class='gsl-button gsl-button-primary' ", $buttonoutput);
                            $buttonoutput = str_replace(array("btn-small"), "", $buttonoutput);
                            echo $buttonoutput;
                        }

                        ?>
                    </div>

                </nav>
            </header>
            <!--/HEADER-->

	        <div id="ysts_system_messages">YSTS_SYSTEM_MESSAGES</div>

            <div class="gsl-content" data-gsl-height-viewport="expand: true;mode: slide">


