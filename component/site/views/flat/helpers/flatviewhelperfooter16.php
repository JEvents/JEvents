<?php
defined('_JEXEC') or die('Restricted access');

function FlatViewHelperFooter16($view)
{
$jinput = JFactory::getApplication()->input;

	if ($jinput->get('pop', '0', 'INT'))
	{
		?>
		<div class="ev_noprint"><p align="center">
				<a href="#close" onclick="if (window.parent == window) {
									self.close();
								} else
									try {window.parent.jQuery('#myEditModal').modal('hide');}catch (e){}
									try {
										window.parent.SqueezeBox.close();
										return false;
									} catch (e) {
										self.close();
										return false;
									}" title="<?php echo JText::_('JEV_CLOSE'); ?>"><?php echo JText::_('JEV_CLOSE'); ?></a>
			</p></div>
		<?php
	}
	$view->loadHelper("JevViewCopyright");
	JevViewCopyright();
	?>
	</div>
	</div>
	<?php
	$dispatcher = JEventDispatcher::getInstance();
	$dispatcher->trigger('onJEventsFooter');

	$task = $jinput->getString('jevtask', '');
	$view->loadModules("jevpostjevents");
	$view->loadModules("jevpostjevents_" . $task);
	$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
	JEVHelper::componentStylesheet($view, "extra.css");
	jimport('joomla.filesystem.file');

	// Lets check if we have editted before! if not... rename the custom file.
	if (JFile::exists(JPATH_SITE . "/components/com_jevents/assets/css/jevcustom.css"))
	{
		// It is definitely now created, lets load it!
		JEVHelper::stylesheet('jevcustom.css', 'components/' . JEV_COM_COMPONENT . '/assets/css/');
	}
	
}
