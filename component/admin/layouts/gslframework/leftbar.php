<?php
/**
 * @version    CVS: JEVENTS_VERSION
 * @package    com_jevents
 * @author     Geraint Edwards <yoursites@gwesystems.com>
 * @copyright  2016-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

use \Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

$leftIconLinks = GslHelper::getLeftIconLinks();

$option = Factory::getApplication()->input->getCmd('option', 'com_jevents');
if ($option == "com_categories")
{
	$option = Factory::getApplication()->input->getCmd('extension', 'com_jevents');
}

$componentParams = ComponentHelper::getParams($option);
$leftmenutrigger = $componentParams->get("leftmenutrigger", 0);

?>
<aside id="left-col" class="gsl-padding-remove  gsl-background-secondary hide-label ">

    <nav class="left-nav-wrap  gsl-width-auto@m"
	    <?php echo $leftmenutrigger == 2 ? "" : ('gsl-navbar="mode: ' . ($leftmenutrigger == 0 ? "hover" : "click") .'"');?>
    >
        <div class="left-logo gsl-background-secondary"
			<?php echo $leftmenutrigger == 2 ? "" : ('gsl-toggle="target:#left-col, #left-col .left-nav, .ysts-page-title; mode: ' . ($leftmenutrigger == 0 ? "hover" : "click") . ';cls: hide-label"') ;?>
        >
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
            <ul class="left-nav gsl-navbar-nav gsl-list hide-label gsl-background-secondary"
	            <?php echo $leftmenutrigger == 2 ? "" : ('gsl-toggle="target:#left-col, #left-col .left-nav, .ysts-page-title; mode: ' . ($leftmenutrigger == 0 ? "hover" : "click") . ';cls: hide-label"') ;?>
            >
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
	                $events = "";
	                if (isset($leftIconLink->events) && count($leftIconLink->events) > 0)
                    {
                        foreach ($leftIconLink->events as $trigger => $action)
                        {
                            $events .= " $trigger = \"$action\"";
                        }
                    }

	                $onclick = $leftmenutrigger == 2 ? "" : 'onclick="if((window.getComputedStyle(this.querySelector(\'.nav-label\')).getPropertyValue(\'display\')==\'none\' && window.innerWidth <= 960) || window.getComputedStyle(this.querySelector(\'.nav-label\')).getPropertyValue(\'display\')!==\'none\') {document.location=this.href;}return false;"';
	                ?>
                    <li class="<?php echo $leftIconLink->class . ($leftIconLink->active ? " gsl-active" : ""); ?>" <?php echo $tooltip;?> <?php echo $events;?>>
	                    <a href="<?php echo $leftIconLink->link; ?>" target="<?php echo isset($leftIconLink->target) ? $leftIconLink->target : "_self"; ?>" <?php echo $onclick;?> >
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
