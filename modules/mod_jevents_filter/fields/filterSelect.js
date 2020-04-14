function setupFilterChoices() {
    var options = jQuery("#filterchoices div");
    options.each(function (idx, opt) {
        jQuery(opt).off("click");
        opt.style.cursor = "pointer";
        jQuery(opt).on("click", function (event) {
            var span = jQuery(opt).find('span');
            var id = span.html();
            span.remove();
            // remove html entities so use firstChild.nodeValue instead of innerHTML
            var text = opt.firstChild.nodeValue;
            jQuery(opt).remove();

            var uls = jQuery("#filtermatches");
            var li = jQuery("<div>", {id: "filter" + id});
            li.append(text);
            if (uls.length) {
                uls.append(li);
                setupFilterLis();
            }
            setupCustomFilterField();
        });
    });
}

var colsbeingsorted = false;

function setupFilterLis() {


    var sortable = document.querySelector('#filtermatches');

    sortable.setAttribute('data-sortable', Sortable.create(sortable, {
        onStart: function (event, ui) {
            colsbeingsorted = true;
        },
        onEnd: function (event, ui) {
            setTimeout(function () {
                colsbeingsorted = false;
                setupCustomFilterField();
            }, 200);
        },
    }));

    var lis = jQuery("#filtermatches div");
    lis.each(function (i, item) {
        item.style.cursor = "pointer";
        jQuery(item).off("click");

        jQuery(item).on("click", function (event) {

            if (colsbeingsorted) {
                return;
            }

            var text = item.innerHTML;
            var id = item.id.replace("filter", "");
            item.parentNode.removeChild(item);

            var sel = jQuery("#filterchoices");
            var opt = jQuery("<div>");
            opt.append(text);
            var span = jQuery("<span>", {'style': 'display:none'});
            span.append(id);
            opt.append(span);
            if (sel) {
                sel.append(opt);
                setupFilterChoices();
            }
            setupCustomFilterField();
        });
    });
}

function setupCustomFilterField() {
    var fieldid = "#jform_params_filters";
    // setup custom field
    var customfield = jQuery(fieldid);
    if (!customfield.length) return;
    customfield.val("");
    var lis = jQuery("#filtermatches div");
    lis.each(function (i, item) {
        if (customfield.val() != "") {
            customfield.val(customfield.val() + ",");
        }
        customfield.val(customfield.val() + item.id.replace("filter", ""));
    });
}

jQuery(window).on("load", function () {
    setupFilterChoices(true);
    setupFilterLis(true);
});
