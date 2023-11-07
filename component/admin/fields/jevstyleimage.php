<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Uri\Uri;

FormHelper::loadFieldClass('hidden');


class FormFieldJevstyleimage extends JFormFieldHidden
{
	protected function getInput()
	{


        $imageroot = $this->element['imageroot'] ? (string) $this->element['imageroot'] : false;
        $imageextension = $this->element['imageextension'] ? (string) $this->element['imageextension'] : false;
        if ($imageroot)
        {
            return "<img src ='" . Uri::root() . $imageroot . $this->value . "." . $imageextension . "' style='height:300px;max-width:100%' />";
        }

	}

}

class_alias("FormFieldJevstyleimage", "JFormFieldJevstyleimage");
