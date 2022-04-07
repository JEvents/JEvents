<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;


extract($displayData);

if (false && $selector == "progressModal")
{
	Factory::getDocument()->addScriptDeclaration("useProgressModal = true;");

	?>
    <div id="<?php echo $selector; ?>" gsl-modal="container:#gslc; bg-close:false" class="gsl-modal-container">
        <div class="gsl-modal-dialog">
            <button class="gsl-modal-close-default" type="button" gsl-close></button>
            <div class="gsl-modal-header info-header">
                <h3 class="gsl-modal-title"><?php echo $params['title']; ?></h3>
            </div>
            <div class="gsl-modal-body">
				<?php echo $body; ?>
            </div>
            <div class="gsl-modal-footer gsl-text-right">
				<?php echo $params['footer']; ?>
            </div>
        </div>
    </div>
	<?php
	return;
}
/**
 * Layout variables
 * ------------------
 * @param   string  $selector  Unique DOM identifier for the modal. CSS id without #
 * @param   array   $params    Modal parameters. Default supported parameters:
 *                             - title        string   The modal title
 *                             - backdrop     mixed    A boolean select if a modal-backdrop element should be included (default = true)
 *                                                     The string 'static' includes a backdrop which doesn't close the modal on click.
 *                             - keyboard     boolean  Closes the modal when escape key is pressed (default = true)
 *                             - closeButton  boolean  Display modal close button (default = true)
 *                             - animation    boolean  Fade in from the top of the page (default = true)
 *                             - url          string   URL of a resource to be inserted as an <iframe> inside the modal body
 *                             - height       string   height of the <iframe> containing the remote resource
 *                             - width        string   width of the <iframe> containing the remote resource
 *                             - bodyHeight   int      Optional height of the modal body in viewport units (vh)
 *                             - modalWidth   int      Optional width of the modal in viewport units (vh)
 *                             - footer       string   Optional markup for the modal footer
 * @param   string  $body      Markup for the modal body. Appended after the <iframe> if the URL option is set
 *
 */

ob_start();
?>
<div id="<?php echo $selector;?>">
    <div class="info-header">
        <h3><?php echo $params['title'];?></h3>
	    <?php echo $body;?>
    </div>
</div>

<?php
$modalHtml = json_encode(ob_get_clean());
Factory::getDocument()->addScriptDeclaration(
        " $selector = $modalHtml;"
);
