<?php

// no direct access
defined('_JEXEC') or die('Restricted access');
if (isset($moddata))
{
	if(is_array($moddata)){
		$count = 0;
		$mode = 0;
		foreach ($moddata as $md){
                    	JPluginHelper::importPlugin('content');
                        

                        $eventdata = new stdClass();
			$eventdata->text = $md;
                        $params = new JRegistry(null);
			$results = JFactory::getApplication()->triggerEvent('onContentPrepare', array('com_jevents', & $eventdata, & $params, 0));
                        $md = $eventdata->text;
                        
			echo "<div class='jevmodrowcount$count jevmodrow$mode' >".$md."</div>";
			$count ++;
			$mode = $count % 2;
		}
	}
	else {
		echo $moddata;
	}
}
