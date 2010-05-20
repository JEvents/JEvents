var $F = function(element) {
	element2 = $(element);
	return element2.getValue();
}

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
		optnode = new Element('option',{value:label+":"+value});
		parent.appendChild(optnode);		
		optnode.text = label;
	},

	insert: function ( fieldName, pluginNode) {

		var textToInsert = '{{' + $F(pluginNode) + '}}';

		// Bail if the selectedIndex is 0 (as this is the 'Select...' option)
		if ( $(pluginNode).selectedIndex == 0 ) return true;

		// insert the text using the library code
		$result = jInsertEditorText(textToInsert,fieldName);

		// reset the selected element back to 'Select...'
		$(pluginNode).selectedIndex = 0;
		return true;
	}
}
