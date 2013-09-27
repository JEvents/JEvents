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
        $conditions=(string) $this->element["conditional"];
        $conditional=(string) $this->element['name'];
        $condarray=(string) $this->element['conditions'];       
        $params = & JComponentHelper::getParams(JEV_COM_COMPONENT);
        $conditionarray=explode(",",$condarray);
        if (in_array($params->get($conditions, "default"), $conditionarray)==TRUE)  $conditionarray[]="global";
        else $extracondition = "";
        $condarray="'".(string) implode("','",$conditionarray)."'";
        $script = <<<SCRIPT
        window.onload = function setupJevConditions(){
            var condition=document.getElementById("jform_params_$conditions");
            condition.setAttribute("onchange", "jevConditions()");
        jevConditions()
        }
        function jevConditions(){var condition=document.getElementById("jform_params_$conditions"); 
            var eventsno = document.getElementById("jform_params_$conditional-lbl"); 
            var hiddencontrol=eventsno.parentNode.parentNode; 
            var conditionsarray=new Array($condarray);  
            if (conditionsarray.indexOf(condition.value)>=0 ) hiddencontrol.style.display="block";
            else hiddencontrol.style.display="none";}               
SCRIPT;
                
        $document = JFactory::getDocument();
        $document->addScriptDeclaration($script);

        return parent::getInput();
    }

}
