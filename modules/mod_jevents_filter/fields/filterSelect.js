function setupFilterChoices(){
	var options = jQuery("#filterchoices div");
	options.each(function(idx, opt){
		jQuery(opt).off("click");
		opt.style.cursor="pointer";
		jQuery(opt).on("click",function(event){
			var span = jQuery(opt).find('span');
			var id = span[0].innerHTML;
			span[0].parentNode.removeChild(span[0]); 
			// remove html entities so use firstChild.nodeValue instead of innerHTML
			var text = opt.firstChild.nodeValue;
			opt.parentNode.removeChild(opt);

			var uls = jQuery("#filtermatches");
			var li = jQuery("<div>",{id:"filter"+id});
			li.append(text);
			if (uls){
				uls.append(li);
				setupFilterLis();
			};
			setupCustomFilterField();
		});
	});
}
function setupFilterLis(){
	var lis = jQuery("#filtermatches div");
	lis.each(function(i, item){
		item.style.cursor="pointer";
		jQuery(item).off("click");

		jQuery(item).on("click",function(event){

			var text = item.innerHTML;  
			var id = item.id.replace("filter","");
			item.parentNode.removeChild(item);  

			var sel = jQuery("#filterchoices");
			var opt = jQuery("<div>");
			opt.append(text);
			var span = jQuery("<span>",{'style':'display:none'});
			span.append(id);
			opt.append(span);
			if (sel){
				sel.append(opt);
				setupFilterChoices();
			};
			setupCustomFilterField();
		});
	});
}

function setupCustomFilterField(){
	var fieldid = "#jform_params_filters";
	// setup custom field
	var customfield = jQuery(fieldid);
	if (!customfield.length) return;
	customfield.value = "";
	var lis = jQuery("#filtermatches div");
	lis.each(function(i, item){
		if (customfield.val() != ""){
			customfield.val(customfield.val()+",");
		}
		customfield.val(customfield.val()+item.id.replace("filter",""));
	});
}

window.addEvent("load",  function() {
	setupFilterChoices(true);setupFilterLis(true);
});
