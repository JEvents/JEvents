<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Factory;

function DefaultViewHelperFooter16($view)
{
	$app   = Factory::getApplication();
	$input = $app->input;

	if ($input->getInt('pop', 0))
	{ ?>
		<div class="ev_noprint"><p align="center">
				<a href="#close"
				   onclick="if (window.parent==window){self.close();} else {try {window.parent.closeJevModalBySelector('#myEditModal,#myDetailModal,#myTranslationModal');}catch (e){}try {window.parent.SqueezeBox.close(); return false;} catch(e) {self.close();return false;}}"
				   title="<?php echo Text::_('JEV_CLOSE'); ?>"><?php echo Text::_('JEV_CLOSE'); ?></a>
			</p></div>
		<?php
	}
	$view->loadHelper("JevViewCopyright");
	JevViewCopyright(); ?>
	</div>
	</div> <!-- close #jevents //-->
	<?php
	$app->triggerEvent('onJEventsFooter');

	$task = $input->getString("jevtask");
	$view->loadModules("jevpostjevents");
	$view->loadModules("jevpostjevents_" . $task);

	JEVHelper::componentStylesheet($view, "extra.css");
	jimport('joomla.filesystem.file');

	// Lets check if we have editted before! if not... rename the custom file.
	if (File::exists(JPATH_SITE . "/components/com_jevents/assets/css/jevcustom.css"))
	{
		// It is definitely now created, lets load it!
		JEVHelper::stylesheet('jevcustom.css', 'components/' . JEV_COM_COMPONENT . '/assets/css/');
	}

}