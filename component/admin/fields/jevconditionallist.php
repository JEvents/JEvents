<?php

/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: jevcategory.php 1987 2011-04-28 09:53:46Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.form.formfield');

class JFormFieldJevconditionallist extends JFormFieldList {

    /**
     * The form field type.
     *
     * @var    string
     * @since  11.1
     */
    protected $type = 'Jevconditionallist';

    protected function getInput() {
        if (is_string($this->value)) {
            $this->value = explode(",", $this->value);
        }
        $params = & JComponentHelper::getParams(JEV_COM_COMPONENT);
        if (in_array($params->get("com_calViewName", "default"), array("ruthin", "iconic", "extplus")))  $extracondition = '|| layout.value=="global"';
        else $extracondition = "";
        $script = <<<SCRIPT
        window.onload = function setupLayoutChange(){
            var layout=document.getElementById("jform_params_com_calViewName");
            layout.setAttribute("onchange", "layoutChange()");
        layoutChange()
        }
        function layoutChange(){var layout=document.getElementById("jform_params_com_calViewName"); 
            var eventsno = document.getElementById("jform_params_com_calEventMenuListRowsPpg-lbl"); 
            var hiddencontrol=eventsno.parentNode.parentNode; 
            if (layout.value=="extplus" || layout.value=="iconic" || layout.value=="ruthin" $extracondition ) hiddencontrol.style.display="block";
            else hiddencontrol.style.display="none";}               
SCRIPT;
                
        $document = JFactory::getDocument();
        $document->addScriptDeclaration($script);

        return parent::getInput();
    }

}
