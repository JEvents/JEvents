function setupColumnChoices(){
	var columnchoices = jevjq("#columnchoices");
	var options = jevjq('#columnchoices div');
	options.each(function(index){
		opt = jevjq(this);
		// remove event handlers
		opt.off("click");
		// This is a disabled group label
		if (!opt.find('span').length){
			opt.css('color',"red");
			return;
		}
		opt.css('cursor',"pointer");
		opt.on("click",function(event){
			var span = jevjq(this).find('span:first-child');
			var id = span.html();
			//span.parent().removeChild(span);
			span.detach();
			// remove html entities so use firstChild.nodeValue instead of innerHTML
			var text =  jevjq(this).html();
			jevjq(this).remove();

			var uls = jevjq("#columnmatches");
			if (uls){
				uls.append("<div id='column"+id+"'>"
						+"<div style='width:200px;display:inline-block;'>"+text+"</div>"
						+"<input type='text' value='"+text+"' style=margin-left:20px;' />"
						+"</div>");
				setupColumnLis();
			};
			setupCustomColumnField("#jevcolumns");
		});
		
	});
}

var colsbeingsorted = false;
function setupColumnLis(){

	jevjq("#columnmatches").sortable({
		start: function(event, ui) {
			colsbeingsorted=true;
		},
		stop: function(event, ui) {
			setTimeout(function() {
				colsbeingsorted=false;
			}, 200);
		},
		update: function(event, ui){
			setupCustomColumnField("#jevcolumns");
		}
	});

	var lis = jevjq("#columnmatches div");
	
	lis.each(function(i){
		item = jevjq(this);
		item.css("cursor","pointer");
		// remove event handlers
		item.off("click");

		item.on("click",function(event){
			if (colsbeingsorted){
				return;
			}

			if (event.target.nodeName.toUpperCase()=="INPUT"){
				return;
			}

			// remove label input
			jevjq(this).find('input').remove();
			var text = jevjq(this).html(); 
			var id = jevjq(this).prop("id").replace("column","");
			jevjq(this).remove()

			var sel = jevjq("#columnchoices");
			if (sel){
				sel.append("<div>"+text+"<span style='display:none'>"+id+"</span><div>");

				setupColumnChoices();
			};
			setupCustomColumnField("#jevcolumns");
		});

		var input = item.find('input');
		if (input.length){
			input.on('change', function(){
				setupCustomColumnField("#jevcolumns");
			});
		}
	});
}

function setupCustomColumnField(fieldid){
	// setup custom field
	var customfield = jevjq(fieldid);
	if (!customfield) return;
	customfield.val( "");
	var lis = jevjq("#columnmatches div");
	lis.each(function( i){
		if (!jevjq(this).prop("id")) return;
		var item = jevjq(this).clone();
		var input = item.find('input');
		var value = input.val();
		// now remove the input element to just get the field label
		input.remove();
		if (customfield.val() != ""){
			customfield.val( customfield.val() + "||");
		}
		// get the contained div
		item = item.find("div");
		customfield.val( customfield.val() + jevjq(this).prop("id").replace("column","")+"|"+ item.html() +"|"+ value);
	});
}
