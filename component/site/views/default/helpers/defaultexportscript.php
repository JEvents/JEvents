<?php
/* 
 *@JEvents Helper for Generating Exports - Script
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;

function DefaultExportScript()
{

	$script = <<<SCRIPT
function clearIcalCategories(allcats){
	if(allcats.checked){
		jevjq('input[name="categories[]"]:checked').each (function(i, el){
			if (el.value!=0){
				el.checked=false;
			}
		});
		jevjq('#othercats').css('display','none');
	}
	else {
		jevjq('input[name="categories[]"]').each (function(i, el){
			if (el.value!=0 && el.checked==false){
				el.checked=true;
			}
		});
		jevjq('#othercats').css('display','block');
	}
}
function clearAllIcalCategories(){
		jevjq('input[name="categories[]"]:checked').each (function(i, el){
			if (el.value==0){
				el.checked=false;
			}
		});
}
function clearIcalYears(allyears){
	if(allyears.checked){
		jevjq('input[name="years[]"]:checked').each (function(i, el){
			if (el.value!=0){
				el.checked=false;
			}
		});
		jevjq('#otheryears').css('display','none');
	}
	else {
		jevjq('input[name="years[]"]').each (function(i, el){
			if (el.value!=0 && el.checked==false){
				el.checked=true;
			}
		});
		jevjq('#otheryears').css('display','block');
	}
}
function clearAllIcalYears(){
		jevjq('input[name="years[]"]:checked').each (function(i, el){
			if (el.value==0){
				el.checked=false;
			}
		});
}

SCRIPT;

	$doc = Factory::getDocument();
	$doc->addScriptDeclaration($script);
}
