<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: cpanel.php 3119 2011-12-20 14:34:33Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2016 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

$jinput = JFactory::getApplication()->input;

// Check if we are saving here.
if ($jinput->get('save', null, null)) {
    customCssSave();
}
?>
<?php if (!empty($this->sidebar)) : ?>
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
 <?php endif; ?>
<div id="jevents">
    <?php

	$file = 'jevcustom.css';
    $srcfile = 'jevcustom.css.new';
    $filepath = JPATH_ROOT . '/components/com_jevents/assets/css/' . $file;
    $srcfilepath = JPATH_ROOT . '/components/com_jevents/assets/css/' . $srcfile;
    if (!JFile::exists($filepath)) {
        $filepath = $srcfilepath;
    }
    $content = '';
    $html = '';

    ob_start();

    $content = JFile::read($filepath);
    $btnclass = "btn btn-success" ;
    $mainspan = 10;
    $fullspan = 12;

    ?>
    <form action="index.php?option=com_jevents" method="post"
          name="admin" id="adminForm">
        <?php echo JHtml::_( 'form.token' ); ?>
        <div id="j-main-container" class="span<?php echo (!empty($this->sidebar)) ? $mainspan : $fullspan; ?>  ">
                <textarea style="width:60%;height:550px;" name="content"><?php echo $content; ?></textarea>
                <input type="hidden" name="controller" value="component" />
                <input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT; ?>" />
                <input type="hidden" name="task" value="" />
                <input type="hidden" name="save" value="custom_css_save" />
            </div>
        </div>
    </form>
    <?php
    $html = ob_get_contents();
    @ob_end_clean();

    echo $html;

    function customCssSave()
    {
        //Check for request forgeries
        JSession::checkToken() or die( 'Invalid Token' );
        $mainframe = JFactory::getApplication();

        $file = 'jevcustom.css';
        $filepath = JPATH_ROOT . '/components/com_jevents/assets/css/' . $file;
        $jinput = JFactory::getApplication()->input;
        $content = $jinput->post->get('content', '', 'RAW');

        $msg = '';
        $msgType = '';

        $status = JFile::write($filepath, $content);
        if (!empty($status)) {
            $msg = JText::_('JEV_CUSTOM_CSS_SUCCESS');
            $msgType = 'notice';
        } else {
            $msg = JText::_('JEV_CUSTOM_CSS_ERROR');
            $msgType = 'error';
        }

        $mainframe->enqueueMessage($msg, $msgType);
        $mainframe->redirect('index.php?option=com_jevents&task=cpanel.custom_css');

    }

    ?>

</div>
