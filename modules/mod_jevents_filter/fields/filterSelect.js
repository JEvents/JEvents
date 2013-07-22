function setupFilterChoices(){
	var fiterchoices = $("filterchoices");
	var options = fiterchoices.getElements('div');
	options.each(function(opt){
		opt.removeEvents("click");
		opt.style.cursor="pointer";
		opt.addEvent("click",function(event){
			var span = opt.getElements('span'); 
			var id = span[0].innerHTML;
			span[0].parentNode.removeChild(span[0]); 
			// remove html entities so use firstChild.nodeValue instead of innerHTML
			var text = opt.firstChild.nodeValue;
			opt.parentNode.removeChild(opt);

			var uls = $("filtermatches");
			var li = new Element("div",{id:"filter"+id});
			li.appendText(text);
			if (uls){
				uls.appendChild(li);
				setupFilterLis();
			};
			setupCustomFilterField();
		});
	});
}
function setupFilterLis(){
	var uls = $("filtermatches");
	var lis = uls.getChildren();
	lis.each(function(item, i){
		item.style.cursor="pointer";
		item.removeEvents("click");

		item.addEvent("click",function(event){

			var text = item.innerHTML;  
			var id = item.id.replace("filter","");
			item.parentNode.removeChild(item);  

			var sel = $("filterchoices");
			var opt = new Element("div");
			opt.appendText(text);
			var span = new Element("span",{'style':'display:none'});
			span.appendText(id);
			opt.appendChild(span);
			if (sel){
				sel.appendChild(opt);
				setupFilterChoices();
			};
			setupCustomFilterField();
		});
	});
}

function setupCustomFilterField(){
	var fieldid = "jform_params_filters";
	// setup custom field
	var customfield = $(fieldid);
	if (!customfield) return;
	customfield.value = "";
	var uls = $("filtermatches");
	var lis = uls.getChildren();
	lis.each(function(item, i){
		if (customfield.value != ""){
			customfield.value+=","
		}
		customfield.value+=item.id.replace("filter","");
	});
}

window.addEvent("domready",  function() {
	setupFilterChoices(true);setupFilterLis(true);
});
