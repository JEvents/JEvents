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
document.addEventListener('DOMContentLoaded', function ()
{
    if (typeof autoSubmitFilter !== "undefined" && autoSubmitFilter)
    {

        var filters = document.querySelectorAll('.jevfilterinput input, .jevfilterinput select')
        filters.forEach(function(filter, index)
        {
            if (
                filter.onchange
                || filter.getAttribute('type') == 'hidden'
                || filter.getAttribute('type') == 'submit'
                || filter.getAttribute('type') == 'reset'
            )
            {
                return;
            }
            console.log(filter);
            ['input'].forEach( evt =>
                filter.addEventListener(evt, function() {
                    document.querySelector('form.jevfiltermodule').submit()
                })
            );

        });

    }

    var filters = document.querySelectorAll('.jevfilterinput input, .jevfilterinput select')
    filters.forEach(function(filter, index) {
        if (
            filter.getAttribute('type') == 'submit'
            || filter.getAttribute('type') == 'reset'
        )
        {
            return;
        }
        if (typeof UIkit !== 'undefined')
        {
            if (filter.nodeName == 'SELECT' && !filter.classList.contains('uk-select'))
            {
                filter.classList.add('uk-select');
            }

        }
        else
        {
            if (filter.nodeName == 'SELECT' && !filter.classList.contains('form-select'))
            {
                filter.classList.add('form-select');
            }

        }

    });

});