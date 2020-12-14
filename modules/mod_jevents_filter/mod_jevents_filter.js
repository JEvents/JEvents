var JeventsFilters = {
    filters: [],
    reset: function (form) {
        // native array
        JeventsFilters.filters.forEach(function (item) {
            if (item.action) {
                eval(item.action);
            }
            else {
                var elem = jQuery("#" + item.id);
                if (!elem.length && form[item.id]) {
                    elem = jQuery(form[item.id]);
                }
                if (elem.length) {
                    var tag = elem.prop('tagName');
                    if (tag.toLowerCase() == 'select') {
                        elem.find('option').each(
                            function (idx, selitem) {
                                selitem.selected = (selitem.value == item.value) ? true : false;
                            }
                        );
                    }
                    else {
                        elem.val(item.value);
                    }
                }
            }
        });
        if (form.filter_reset) {
            form.filter_reset.value = 1;
        }
        form.submit();
    }
};