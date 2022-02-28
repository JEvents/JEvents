<?php


defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;
use Joomla\Utilities\ArrayHelper;

extract($displayData);

/**
 * Layout variables
 * -----------------
 * @var   string  $selector  Unique DOM identifier for the modal. CSS id without #
 * @var   array   $params    Modal parameters. Default supported parameters:
 *                             - title        string   The modal title
 *                             - keyboard     boolean  Closes the modal when escape key is pressed (default = true)
 *                             - closeButton  boolean  Display modal close button (default = true)
 *                             - url          string   URL of a resource to be inserted as an <iframe> inside the modal body
 *                             - footer       string   Optional markup for the modal footer
 * @var   string  $body      Markup for the modal body. Appended after the <iframe> if the URL option is set
 */

$modalClasses = array('uk-modal');


$modalAttributes = array(
	'tabindex' => '-1',
	'class'    => ' ' .implode(' ', $modalClasses)
);


if (isset($params['url']))
{
	$url        = 'data-url="' . $params['url'] . '"';
	$iframeHtml = htmlspecialchars(LayoutHelper::render('jevents.modal.iframe', $displayData), ENT_COMPAT, 'UTF-8');
}
?>
<div id="<?php echo $selector; ?>" data="uk-modal" role="dialog" <?php echo ArrayHelper::toString($modalAttributes); ?>>
	<div class="uk-modal-dialog ">
		<?php
			// Header
			if (!isset($params['closeButton']) || isset($params['title']) || $params['closeButton'])
			{
				echo LayoutHelper::render('jevents.modal.header', $displayData);
			}

			// Body
			echo LayoutHelper::render('jevents.modal.body', $displayData);

			// Footer
			if (isset($params['footer']))
			{
				echo LayoutHelper::render('jevents.modal.footer', $displayData);
			}
		?>
	</div>
</div>
