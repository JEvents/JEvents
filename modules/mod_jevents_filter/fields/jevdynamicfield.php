<?php

/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: jevcategorynew.php 2983 2011-11-10 14:02:23Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

use Joomla\CMS\Access\Access;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\PluginHelper;

jimport('joomla.form.helper');
FormHelper::loadFieldClass('text');

#[\AllowDynamicProperties]
class JFormFieldJevdynamicfield extends JFormFieldText
{

	protected
		$type = 'Jevdynamicfield';

	protected
	function getInput()
	{
        $editor = $this->getAttribute('editor');
        ob_start();
        ?>
        <select id="<?php echo $this->fieldname;?>" class="form-select gsl-select" onchange="Joomla.editors.instances['<?php echo $editor;?>'].replaceSelection(this.value);" >
            <option value="1">something</option>
            <option value="2">something else</option>
        </select>
        <?php
        $html = ob_get_clean();
        return $html;
	}


}
