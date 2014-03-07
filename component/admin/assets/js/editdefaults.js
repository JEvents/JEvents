// JQuery Done
// JQuery version
/*
defaultsEditorPlugin = {
	update: function (pluginName) {
		var pluginNode = jQuery(pluginName);
		while (pluginNode.firstChild) {
			pluginNode.removeChild(pluginNode.firstChild);
		}

	},
	optgroup: function(pluginNode, label){
		var group = jQuery("<optgroup/>" , {'label':label})
		jQuery(pluginNode).append(group);
		return group;
	},
	node: function(parent, label, value){
		var optnode = jQuery('<option>'+label+'</option>',{value:label+":"+value});
		jQuery(parent).append(optnode);
	},

	insert: function ( fieldName, pluginNode) {
		var sel = jQuery(pluginNode);
		// MSIE 8 problem with mootools plugin enabled!
		var textToInsert = '{{' + sel.value + '}}';

		// Bail if the selectedIndex is 0 (as this is the 'Select...' option)
		if ( jQuery(pluginNode).selectedIndex == 0 ) return true;

		// insert the text using the library code
		$result = jInsertEditorText(textToInsert,fieldName);

		// reset the selected element back to 'Select...'
		jQuery(pluginNode).selectedIndex = 0;
		// needed for MSIE 9 bug - see jQuery(pluginNode)
                                    for (var i=0; i<sel.length; i++){
                                        sel.options[i].selected = false;
                                    }
                                    var test = jQuery(pluginNode).options;
		return false;
	}
}

 */
defaultsEditorPlugin = {
	update: function (pluginName) {
		var pluginNode = $(pluginName);
		while (pluginNode.firstChild) {
			pluginNode.removeChild(pluginNode.firstChild);
		}

	},
	optgroup: function(pluginNode, label){
		var group = new Element('optgroup', {'label':label});
		pluginNode.appendChild(group);
		return group;
	},
	node: function(parent, label, value){
		var optnode = new Element('option',{value:label+":"+value});
		parent.appendChild(optnode);
		optnode.text = label;
	},

	insert: function ( fieldName, pluginNode) {
		var sel = $(pluginNode);
		// MSIE 8 problem with mootools plugin enabled!
		var textToInsert = '{{' + sel.value + '}}';

		// Bail if the selectedIndex is 0 (as this is the 'Select...' option)
		if ( jQuery(pluginNode).selectedIndex == 0 ) return true;

		// insert the text using the library code
		$result = jInsertEditorText(textToInsert,fieldName);

		// reset the selected element back to 'Select...'
		jQuery(pluginNode).selectedIndex = 0;
		// needed for MSIE 9 bug - see jQuery(pluginNode)
                                    for (var i=0; i<sel.length; i++){
                                        sel.options[i].selected = false;
                                    }
                                    var test = $(pluginNode).options;
		return false;
	}
}
