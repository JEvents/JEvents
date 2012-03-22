<?php 
defined('_JEXEC') or die('Restricted access');
		
function DefaultViewHelperFooter16($view){
if (JRequest::getInt('pop', 0)) { ?>
	<div class="ev_noprint"><p align="center">
	<a href="#close" onclick="if (window.parent==window){self.close();} else try {window.parent.SqueezeBox.close(); return false;} catch(e) {self.close();return false;}" title="<?php echo JText::_('JEV_CLOSE');?>"><?php echo JText::_('JEV_CLOSE');?></a>
	</p></div>
<?php
}
$view->loadHelper("JevViewCopyright");
JevViewCopyright(); ?>
</div>
<?php
	$dispatcher	=& JDispatcher::getInstance();
	$dispatcher->trigger( 'onJEventsFooter');

	$task = JRequest::getString("jevtask");
	$view->loadModules("jevpostjevents");
	$view->loadModules("jevpostjevents_".$task);
	
}