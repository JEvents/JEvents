<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

// Do not do this in Internet Explorer 10 or lower (Note that MSIE 11 changed the app name to Trident)
if (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") !== false || strpos($_SERVER['HTTP_USER_AGENT'], "Internet Explorer") !== false))
{
    return;
}

use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Factory;

// TODO - Remove rands below before release.

$document = JFactory::getDocument();
JHtml::stylesheet('media/com_jevents/css/uikit.gsl.css', array('version' => '1.7.4', 'relative' => false));
JHtml::stylesheet('administrator/components/com_jevents/assets/css/jevents.css', array('version' => '1.7.4', 'relative' => false));
JHtml::script('media/com_jevents/js/uikit.js', array('version' => '1.7.4', 'relative' => false));
JHtml::script('media/com_jevents/js/uikit-icons.js', array('version' => '1.7.4', 'relative' => false));
JHtml::script('administrator/components/com_jevents/assets/js/jevents.js', array('version' => '1.7.4', 'relative' => false));

// set container scope for code
$document->addScriptDeclaration("gslUIkit.container = '.gsl-scope';");

// Check if master site is SSL IFF not running locally
if (!JUri::getInstance()->isSsl())
{
	$localhost = @gethostbyname('localhost');
	if (empty($localhost) || !isset($_SERVER["SERVER_ADDR"]) || $localhost !== $_SERVER["SERVER_ADDR"])
	{
		$params = JComponentHelper::getParams('com_jevents');
		if (!$params->get('hidewarnings', 0))
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_YOURSITES_MASTER_HTTPS_WARNING'), 'warning');
		}
	}
}

?>
<div class="gsl-scope" id="gslc"> <!-- Open Custom UiKit Container -->
    <?php
    // Progress Modal
    $whendonemessage = JText::_("COM_YOURSITES_CLOSE_PROGRESS_POPUP", true);
    $progresstitle   = JText::_("COM_YOURSITES_PROGRESS_POPUP_TITLE", true);
    $progressModalData = array(
    'selector' => 'progressModal',
    'params'   => array(
    'title'  => $progresstitle,
    'footer' => "<strong>$whendonemessage </strong>",
    'backdrop' => 'static'
    ),
    'body'     => '<div class="gsl-grid gsl-padding-remove" style="padding:10px;" ><div id="pmcol1" class="gsl-width-1-2"></div><div  id="pmcol2" class="gsl-width-1-2"></div></div>',
    );

    echo JLayoutHelper::render('yoursites.modal.main', $progressModalData);
    ?>
    <div class="gsl-margin-remove" gsl-grid>
        <!-- LEFT BAR -->
		<?php echo LayoutHelper::render('gslframework.leftbar', null,  dirname( __DIR__, 1) ); ?>
        <!-- /LEFT BAR -->
        <div id="right-col" class="gsl-padding-remove gsl-width-expand@m ">

            <!--HEADER-->
            <header id="top-head">
                <nav class="gsl-navbar-container gsl-background-secondary ys-titlebar" gsl-navbar>
                    <div class="gsl-navbar-left gsl-background-secondary gsl-width-expand@m">
                        <?php
                            echo Factory::getApplication()->JComponentTitle;
                        ?>
                    </div>
                    <div class="gsl-navbar-right  gsl-background-secondary ">
                        <ul class="gsl-navbar-nav ">
                            <?php
                            if (JEVHelper::isAdminUser())
                            {
                                ?>
                                <li class="hasYsPopover ys_support"
                                    data-yspoptitle="<?php echo JText::_('COM_YOURSITES_SUPPORT_FORUM', true); ?>"
                                    data-yspopcontent="<?php echo JText::_('COM_YOURSITES_SUPPORT_FORUM_TOOLTIP', true); ?>"
                                >
                                    <a href="https://www.jevents.net/discussions"
                                       data-gsl-icon="icon: question"
                                       title="<?php JText::_('COM_YOURSITES_SUPPORT_FORUM'); ?>"
                                       class="gsl-icon"
                                       target="_blank"
                                       aria-expanded="false">
                                    </a>
                                </li>
                                <li class="hasYsPopover  ys_docs"
                                    data-yspoptitle="<?php echo JText::_('COM_YOURSITES_DOCUMENTATION', true); ?>"
                                    data-yspopcontent="<?php echo JText::_('COM_YOURSITES_DOCUMENTATION_TOOLTIP', true); ?>"
                                >
                                    <a href="https://www.yoursitesjevents.net/documentation"
                                       data-gsl-icon="icon: file-text"
                                       title="<?php JText::_('COM_YOURSITES_DOCUMENTATION'); ?>"
                                       class="gsl-icon"
                                       target="_blank"
                                       aria-expanded="false">
                                    </a>
                                </li>

                                <li class="hasYsPopover  ys_config"
                                    data-yspoptitle = "<?php echo  JText::_('COM_YOURSITES_CONFIG', true); ?>"
                                    data-yspopcontent = "<?php echo JText::_('COM_YOURSITES_CONFIG_TOOLTIP', true); ?>"
                                    >
                                    <a href="<?php echo JUri::base() . 'index.php?option=com_jevents&task=params.edit&view=component&component=com_jevents';?>"
                                       data-gsl-icon="icon: settings"
                                       title="<?php JText::_('COM_YOURSITES_CONFIG'); ?>"
                                       class="gsl-icon"
                                       aria-expanded="false">
                                    </a>
                                </li>
	                            <?php
                            }
                            ?>
                            <li>
                                <a href="<?php echo JUri::base() . 'index.php?option=com_login&amp;task=logout&amp;' . JSession::getFormToken() . '=1'; ?>"
                                   data-gsl-icon="icon:  sign-out"
                                   title="<?php JText::_('JLOGOUT'); ?>"
                                   data-gsl-tooltip="<?php JText::_('JLOGOUT'); ?>" class="gsl-icon"
                                   aria-expanded="false">
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
                <nav class="gsl-navbar-container ys-gsl-action-buttons" gsl-navbar>
                    <div class="gsl-navbar-left gsl-background-primary gsl-width-expand@m">
	                    <?php
	                    $bar = JToolBar::getInstance('toolbar2');
	                    $toolbarButtons = $bar->getItems();

	                    if (!count($toolbarButtons))
                        {
	                        $bar = JToolBar::getInstance('toolbar');
	                        $toolbarButtons = $bar->getItems();
                        }

	                    foreach ($toolbarButtons as $toolbarButton)
                        {
                            $buttonoutput = $bar->renderButton($toolbarButton);
	                        $buttonoutput = str_replace("btn ", "gsl-button gsl-button-primary ", $buttonoutput);
	                        $buttonoutput = str_replace('class=""', "class='gsl-button gsl-button-primary' ", $buttonoutput);
	                        $buttonoutput = str_replace(array("btn-small"), "", $buttonoutput);
	                        echo $buttonoutput  ;
                        }

	                    ?>
                    </div>

                </nav>
            </header>
            <!--/HEADER-->

            <div id="ysts_system_messages"></div>

            <div class="gsl-content" data-gsl-height-viewport="expand: true;mode: slide">


