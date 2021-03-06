// JQuery version

defaultsEditorPlugin = {
    update: function (pluginName) {
        var pluginNode = jevjq(pluginName);
        while (pluginNode.firstChild) {
            pluginNode.removeChild(pluginNode.firstChild);
        }

    },
    optgroup: function (pluginNode, label) {
        var group = jevjq("<optgroup/>", {'label': label})
        jevjq(pluginNode).append(group);
        return group;
    },
    node: function (parent, label, value) {
        var optnode = jevjq('<option value="' + label + ":" + value + '">' + label + '</option>');
        jevjq(parent).append(optnode);
    },

    insert: function (fieldName, pluginNode) {
        var sel = jevjq("#" + pluginNode + " option:selected");

        // Bail if the selectedIndex is 0 (as this is the 'Select...' option)
        if (jevjq("#" + pluginNode).selectedIndex == 0) return true;

        // MSIE 8 problem with mootools plugin enabled!
        var textToInsert = '{{' + sel.val() + '}}';

        // insert the text using the library code
        $result = jInsertEditorText(textToInsert, fieldName);

        // reset the selected element back to 'Select...'
        jevjq(pluginNode).selectedIndex = 0;
        // needed for MSIE 9 bug - see jevjq(pluginNode)
        sel.each(function (index, selel) {
            jQuery(selel).attr('selected', false);
        });
        return false;
    },

    inject: function (fieldName, textToInsert) {

        // clear the current value
        try {
            Joomla.editors.instances[fieldName].setValue('');
        }
        catch (e)
        {
            try {
                document.getElementById(fieldName).value = '';
            }
            catch (e)
            {

            }
        }
        // insert the text using the library code
        $result = jInsertEditorText(textToInsert, fieldName);
        return false;
    },
    extract: function (fieldName) {

        // insert the text using the library code
        try {
            return Joomla.editors.instances[fieldName].getValue();
        }
        catch (e)
        {
            try {
                return document.getElementById(fieldName).value;
            }
            catch (e)
            {

            }
        }
        return '';
    }


}
