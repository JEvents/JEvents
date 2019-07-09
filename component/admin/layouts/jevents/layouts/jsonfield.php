<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_admin
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

extract($displayData);


?>
    <td>
		<?php
        $jsonsrc   = $layoutfield->jsonsrc;
		$jsonfield = $layoutfield->jsonfield;
		$jsonfield_info = $layoutfield->jsonfield . "_info";

		if (isset($item->$jsonsrc->$jsonfield_info))
		{
			$tooltip = '  data-yspopcontent = "' . addslashes($item->$jsonsrc->$jsonfield_info) . '" ';
			$tooltipclass = " hasYsPopover";

		}
		else
        {
	        $tooltip = "";
	        $tooltipclass = " ";
        }

		if (!isset($item->$jsonsrc) ||  empty((array) $item->$jsonsrc))
        {
	        ?>
            <div class="gsl-button gsl-button-small gsl-button-default <?php echo $tooltipclass;?>" <?php echo $tooltip;?>>
                <span gsl-icon="icon: question"></span>
            </div>
	        <?php
        }
		else if (!isset($item->$jsonsrc->$jsonfield))
        {
            ?>
            <div class="gsl-button gsl-button-small gsl-button-warning <?php echo $tooltipclass;?>" <?php echo $tooltip;?>>
                <span gsl-icon="icon: question"></span>
            </div>
            <?php
        }
		else
		{
			switch ($item->$jsonsrc->$jsonfield)
			{
				case -1:
				    ?>
                    <div class="gsl-button gsl-button-small gsl-button-danger <?php echo $tooltipclass;?>" <?php echo $tooltip;?>>
                        <span gsl-icon="icon: close"></span>
                    </div>
                    <?php
					break;
				case 0:
					?>
                    <div class="gsl-button gsl-button-small gsl-button-default <?php echo $tooltipclass;?>" <?php echo $tooltip;?>>
                        <span gsl-icon="icon: minus"></span>
                    </div>
					<?php
					break;
				case 1:
					?>
                    <div class="gsl-button gsl-button-small gsl-button-success <?php echo $tooltipclass;?>" <?php echo $tooltip;?>>
                        <span gsl-icon="icon: check"></span>
                    </div>
					<?php
					break;
				default:
					?>
                    <div class="gsl-button gsl-button-small gsl-button-default <?php echo $tooltipclass;?>" <?php echo $tooltip;?>>
                        <?php echo $item->$jsonsrc->$jsonfield;?>
                    </div>
					<?php
					break;
			}
		}
		?>
    </td>