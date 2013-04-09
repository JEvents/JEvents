<?php 
/* 
 *@JEvents Helper for Generating Exports - Script
 */

defined('_JEXEC') or die('Restricted access');

function DefaultExportScript () {

$script = <<<SCRIPT
function clearIcalCategories(allcats){
	if(allcats.checked){
		document.getElements('input[name=categories[]]:checked').each (function(el){
			if (el.value!=0){
				el.checked=false;
			}
		});
		$('othercats').style.display='none';
	}
	else {
		document.getElements('input[name=categories[]]').each (function(el){
			if (el.value!=0 && el.checked==false){
				el.checked=true;
			}
		});
		$('othercats').style.display='block';		
	}
}
function clearAllIcalCategories(){
		document.getElements('input[name=categories[]]:checked').each (function(el){
			if (el.value==0){
				el.checked=false;
			}
		});
}
function clearIcalYears(allyears){
	if(allyears.checked){
		document.getElements('input[name=years[]]:checked').each (function(el){
			if (el.value!=0){
				el.checked=false;
			}
		});
		$('otheryears').style.display='none';		
	}
	else {
		document.getElements('input[name=years[]]').each (function(el){
			if (el.value!=0 && el.checked==false){
				el.checked=true;
			}
		});
		$('otheryears').style.display='block';				
	}
}
function clearAllIcalYears(){
		document.getElements('input[name=years[]]:checked').each (function(el){
			if (el.value==0){
				el.checked=false;
			}
		});
}

SCRIPT;

$doc = JFactory::getDocument();
$doc->addScriptDeclaration($script);
}
