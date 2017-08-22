function setupColumnChoices(){
	var columnchoices = jQuery("#columnchoices");
	var options = jQuery('#columnchoices div');
	options.each(function(index){
		opt = jQuery(this);
		// remove event handlers
		opt.off("click");
		// This is a disabled group label
		if (!opt.find('span').length){
			opt.css('color',"red");
			return;
		}
		opt.css('cursor',"move");
		opt.on("click",function(event){
			var span = jQuery(this).find('span:first-child');
			var id = span.html();

			//span.parent().removeChild(span);
			span.detach();
			// remove html entities so use firstChild.nodeValue instead of innerHTML
			var text =  jQuery(this).html();
			jQuery(this).remove();

			var uls = jQuery("#columnmatches");
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

	jQuery("#columnmatches").sortable({
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
		},
                cancel:'#columnmatches_heading',
                handle:'.sortablehandle'
	});        

	var lis = jQuery("#columnmatches > div");
	
	lis.each(function(i){
		item = jQuery(this);
                if (item.prop("id")=="columnmatches_heading"){
                    return;
                }
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
			var text = jQuery(this).find("input").val();
			jQuery(this).find('input').remove();
			var id = jQuery(this).prop("id").replace("column","");

			jQuery(this).remove()
			var sel = jQuery("#columnchoices");
			if (sel){
				sel.append("<div>"+text+"<span style='display:none'>"+id+"</span></div>");

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
	var customfield = jQuery(fieldid);
	if (!customfield) return;
	customfield.val( "");
	var lis = jQuery("#columnmatches div");
	lis.each(function( i){
		if (!jQuery(this).prop("id")) return;
                if (jQuery(this).prop("id")=="columnmatches_heading"){
                    return;
                }                
		var item = jQuery(this).clone();
		var input = item.find('input');
		var value = input.val();
		// now remove the input element to just get the field label
		input.remove();
		if (customfield.val() != ""){
			customfield.val( customfield.val() + "||");
		}
		// get the contained div
		item = item.find("div");
		customfield.val( customfield.val() + jQuery(this).prop("id").replace("column","")+"|"+ item.html() +"|"+ value);
	});
}
