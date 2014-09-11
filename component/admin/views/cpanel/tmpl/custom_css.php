<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: cpanel.php 3119 2011-12-20 14:34:33Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');
if (JRequest::getVar('save'))
{
	customCssSave();
}
?>
<div id="jevents">
	<?php
	if (isset($this->warning))
	{
		?>
		<dl id="system-message">
			<dt class="notice">Message</dt>
			<dd class="notice fade">
				<ul>
					<li><?php echo $this->warning; ?></li>
				</ul>
			</dd>
		</dl>   	
		<?php
	}

	$file = 'jevcustom.css';
	$srcfile = 'jevcustom.css.new';
	$filepath = JPATH_ROOT . '/components/com_jevents/assets/css/' . $file;
	$srcfilepath = JPATH_ROOT . '/components/com_jevents/assets/css/' . $srcfile;
	if (!JFile::exists($filepath))
	{
		$filepath = $srcfilepath;
	}
	$content = '';
	$html = '';
	$msg = JRequest::getVar('msg', '', 'GET');
	$msgType = JRequest::getVar('msgtype', '', 'GET');

	ob_start();

	$content = JFile::read($filepath);
	$btnclass = JevJoomlaVersion::isCompatible("3.0")? "btn btn-success" : "";
	?>

	<form action="index.php?option=com_jevents&task=cpanel.custom_css&save=custom_css_save" method="post" name="custom_css_save" id="custom_css_save">
			<?php if (!empty($this->sidebar)) : ?>
			<div id="j-sidebar-container" class="span2">
				<?php echo $this->sidebar; ?>
			</div>
			<div id="j-main-container" class="span10">
				<?php else : ?>
				<div id="j-main-container">
<?php endif; ?>
				<textarea style="width:90%;height:650px;" name="content"><?php echo $content; ?></textarea>
				<input type="submit" style="display:block;margin-left:2px;" name="save" class="<?php echo $btnclass;?>" value="<?php echo JText::_('JEV_CSS_SAVE'); ?>">
				</form>
				<?php
				$html = ob_get_contents();
				@ob_end_clean();

				echo $html;

				function customCssSave()
				{
					$mainframe = JFactory::getApplication();
					$file = 'jevcustom.css';
					$filepath = JPATH_ROOT . '/components/com_jevents/assets/css/' . $file;
					$content = JRequest::getVar('content', '', 'POST', '', JREQUEST_ALLOWRAW);
					$msg = '';
					$msgType = '';

					$status = JFile::write($filepath, $content);
					if (!empty($status))
					{
						$msg = JText::_('JEV_CUSTOM_CSS_SUCCESS');
						$msgType = 'info';
					}
					else
					{
						$msg = JText::_('JEV_CUSTOM_CSS_ERROR');
						$msgType = 'error';
					}

					$mainframe->enqueueMessage($msg, $msgType);
					$mainframe->redirect('index.php?option=com_jevents&task=cpanel.custom_css&msg=' . $msg . '&msgtype=' . $msgType . '');

				}
				?>

			</div>
