<?php
/**
 * @version    CVS: 3.5.0dev
 * @package    com_yoursites
 * @author     Geraint Edwards <yoursites@gwesystems.com>
 * @copyright  2016-2019 GWE Systems Ltd
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

use \Joomla\CMS\Factory;

$leftIconLinks = GslHelper::getLeftIconLinks();

Factory::getDocument()->addScriptDeclaration('ys_popover(".hasYsPopover");');

?>
<aside id="left-col" class="gsl-padding-remove  gsl-background-secondary hide-label ">

    <nav class="left-nav-wrap  gsl-width-auto@m gsl-navbar-container" gsl-navbar>
        <div class="left-logo gsl-background-secondary"  gsl-toggle="target:#left-col, #left-col .left-nav, .ysts-page-title; mode: hover;cls: hide-label">
            <div>
                <?php
                GslHelper::cpanelIconLink();
                ?>
            </div>
        </div>

        <div class="gsl-navbar gsl-background-secondary"  >
            <?php
            ob_start();
            ?>
            <ul class="left-nav gsl-navbar-nav gsl-list hide-label gsl-background-secondary" gsl-toggle="target:#left-col, #left-col .left-nav, .ysts-page-title; mode: hover;cls: hide-label">
                <?php
                foreach ($leftIconLinks as $leftIconLink)
                {
	                $tooltip = "";
	                if (!empty($leftIconLink->tooltip))
                    {
	                    $leftIconLink->class .= " hasYsPopover ";
	                    $tooltip = " data-yspoptitle='$leftIconLink->tooltip' ";
	                    if(!empty($leftIconLink->tooltip_detail))
	                    {
		                    $tooltip .= "data-yspopcontent='$leftIconLink->tooltip_detail'";
	                    }
                    }

	                ?>
                    <li class="<?php echo $leftIconLink->class . ($leftIconLink->active ? " gsl-active" : ""); ?>" <?php echo $tooltip;?>>
                        <a href="<?php echo $leftIconLink->link; ?>" target="<?php echo isset($leftIconLink->target) ? $leftIconLink->target : "_self"; ?>">
                            <span data-gsl-icon="icon: <?php echo $leftIconLink->icon; ?>" class="gsl-margin-small-right"></span>
                            <span class="nav-label"><?php echo $leftIconLink->label; ?></span>
                        </a>
                    </li>
	                <?php
                }

                GslHelper::returnToMainComponent();
                ?>
            </ul>
            <?php
            $leftNav = ob_get_clean();
            // Strip white spaces since they take up space in the inline-block version of the narrow view
            $leftNav = preg_replace('/(\>)\s*(\<)/m', '$1$2', $leftNav);
            echo $leftNav;
            ?>
        </div>
    </nav>
</aside>
