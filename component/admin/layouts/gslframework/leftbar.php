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

$script = <<< SCRIPT
var leftMenuTrigger = $leftmenutrigger;
SCRIPT;
	Factory::getApplication()->getDocument()->addScriptDeclaration($script);

?>
<aside id="left-col" class="gsl-padding-remove  gsl-background-secondary <?php echo $leftmenutrigger == 3 ? '' : 'hide-label';?> ">

    <nav class="left-nav-wrap  gsl-width-auto@m gsl-navbar"
	    <?php
	    if ($leftmenutrigger != 3)
	    {
		    echo $leftmenutrigger == 2 ? "" : ('gsl-navbar="mode: ' . ($leftmenutrigger == 0 ? "hover" : "click") . '"');
	    }
		?>
        >
        <div class="left-logo gsl-background-secondary gsl-toggle"
			<?php
			if ($leftmenutrigger != 3)
			{
				echo $leftmenutrigger == 2 ? "" : ('gsl-toggle="target:#left-col, #left-col .left-nav, .ysts-page-title; mode: ' . ($leftmenutrigger == 0 ? "hover" : "click") . ';cls: hide-label"');
			}
			?>
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
            <ul class="left-nav gsl-navbar-nav gsl-list gsl-background-secondary gsl-toggle <?php echo $leftmenutrigger == 3 ? '' : 'hide-label';?> "
	            <?php
	            if ($leftmenutrigger != 3)
	            {
		            echo $leftmenutrigger == 2 ? "" : ('gsl-toggle="target:#left-col, #left-col .left-nav, .ysts-page-title; mode: ' . ($leftmenutrigger == 0 ? "hover" : "click") . ';cls: hide-label"');
		            }
				?>
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
		                    <?php if (!empty($leftIconLink->icon)) { ?>
                            <span data-gsl-icon="icon: <?php echo $leftIconLink->icon; ?>" class="gsl-margin-small-right"></span>
		                    <?php } else if (!empty($leftIconLink->iconSrc)) { ?>
			                    <span class="gsl-margin-small-right"><img src="<?php echo $leftIconLink->iconSrc; ?>" /></span>
		                    <?php } ?>
		                    <span class="nav-label"><?php echo $leftIconLink->label; ?></span>
                        </a>
	                    <?php
	                    if (isset($leftIconLink->sublinks) && count($leftIconLink->sublinks))
	                    {
		                    ?>
		                    <div class="gsl-dropdown  gsl-background-secondary" gsl-dropdown='{"mode": "click, hover", "delay-hide":100, "offset":0 ,"pos":"right-top"}'>
			                    <ul class="gsl-padding-remove">
				                    <?php
				                    foreach ( $leftIconLink->sublinks as $sublink)
				                    {
					                    ?>
					                    <li class="gsl-padding-remove">
						                    <button onclick="<?php echo $sublink->onclick; ?>"
						                            class="<?php echo $sublink->class; ?>">
                                    <span gsl-icon="icon: <?php echo $sublink->icon; ?>"
                                          class="<?php echo $sublink->iconclass; ?>">
                                    </span>
							                    <?php echo $sublink->label; ?>
						                    </button>
					                    </li>
					                    <?php
				                    }
				                    ?>
			                    </ul>
		                    </div>
		                    <?php
	                    }
	                    ?>
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
