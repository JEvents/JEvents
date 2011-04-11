<?php 
defined('_JEXEC') or die('Restricted access');
		
function DefaultViewHelperFooter($view){
if (JVersion::isCompatible("1.6.0")){
	return $view->_footer16();
}
if (JRequest::getInt('pop', 0)) { ?>
	<div class="ev_noprint"><p align="center">
	<a href="#close" onclick="if (window.parent==window){self.close();} else try {window.parent.SqueezeBox.close(); return false;} catch(e) {seld.close();return false;}" title="<?php echo JText::_('JEV_CLOSE');?>"><?php echo JText::_('JEV_CLOSE');?></a>
	</p></div>
<?php
}
$view->loadHelper("JevViewCopyright");
JevViewCopyright(); ?>
	</td>
	</tr>
</table>
<?php
	$dispatcher	=& JDispatcher::getInstance();
	$dispatcher->trigger( 'onJEventsFooter');

}