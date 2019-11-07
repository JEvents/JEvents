function jevEditPopup(url) {
    /** close dialog may not exist for monthly calendar */
    try {
        jQuery('.action_dialogJQ').modal('hide');
    }
    catch (e) {
    }
    addEditModalHtml();
    /** see http://stackoverflow.com/questions/16152275/how-to-resize-twitter-bootstrap-modal-dynamically-based-on-the-content */
    jQuery('#myEditModal .modal-header').css({'display': 'block'});
    jQuery('#myEditModal .modal-title').html(Joomla.JText._("JEV_ADD_EVENT"));
    /** jQuery('#myEditModal .modal-body').css({  'overflow-y':'auto'}); */
    launchModal('#myEditModal', url);

    /** consider using https://github.com/noelboss/featherlight/#usage instead ! */

}

function jevEditTranslation(url, title) {
    /** close dialog may not exist for monthly calendar */
    try {
        jQuery('.action_dialogJQ').modal('hide');
    }
    catch (e) {
    }
    addEditModalHtml();
    /** see http://stackoverflow.com/questions/16152275/how-to-resize-twitter-bootstrap-modal-dynamically-based-on-the-content */
    jQuery('#myEditModal .modal-header').css({'display': 'block'});
    jQuery('#myEditModal .modal-title').html(title);
    /** jQuery('#myEditModal .modal-body').css({  'overflow-y':'auto'}); */
    launchModal('#myEditModal', url);

    /** consider using https://github.com/noelboss/featherlight/#usage instead ! */

}

function jevEditPopupNoHeader(url) {
    /** close dialog may not exist for monthly calendar */
    try {
        jQuery('.action_dialogJQ').modal('hide');
    }
    catch (e) {
    }
    addEditModalHtml();

    /** Will be true if bootstrap 3 is loaded, false if bootstrap 2 or no bootstrap */
    var bootstrap3_enabled = (typeof jQuery().emulateTransitionEnd == 'function');

    /** see http://stackoverflow.com/questions/16152275/how-to-resize-twitter-bootstrap-modal-dynamically-based-on-the-content */
    jQuery('#myEditModal .modal-body').css({'top': '5px'});
    jQuery('#myEditModal .modal-header').css({'display': 'none'});
    launchModal('#myEditModal', url);

}

function jevEditPopupNoTitle(url) {
    /** close dialog may not exist for monthly calendar */
    try {
        jQuery('.action_dialogJQ').modal('hide');
    }
    catch (e) {
    }
    addEditModalHtml();

    /** see http://stackoverflow.com/questions/16152275/how-to-resize-twitter-bootstrap-modal-dynamically-based-on-the-content */
    jQuery('#myEditModal .modal-body').css({'top': '5px'});
    jQuery('#myEditModal .modal-header .close').css({
        'margin-right': '-15px',
        'margin-top': '-15px',
        'opacity': 1,
        'font-size:': '30px'
    });
    jQuery('#myEditModal .modal-header ').css({'height': '0px', 'z-index': '99', 'border': 'none'});
    jQuery('#myEditModal .modal-header .modal-title').css({'display': 'none'});

    launchModal('#myEditModal', url);

}

function launchModal(selector, url) {
    /** Clear the old page! */
    jQuery(selector + ' iframe').attr("src", "about:blank");
    /** Will be true if bootstrap 3 is loaded, false if bootstrap 2 or no bootstrap */
    var bootstrap3_enabled = (typeof jQuery().emulateTransitionEnd == 'function');
    if (bootstrap3_enabled) {
        jQuery(selector).off('shown.bs.modal');
        jQuery(selector).on('shown.bs.modal', function () {
            /*jQuery(selector+' iframe').attr("src","about:blank");*/
            /* scrolling issue in iOS 11.3*/
            var scrollT = jQuery(window).scrollTop();
            if (scrollT > 0) {
                jQuery(selector).data('scrollTop', scroll);
            }
            jQuery('body').css({
                position: 'fixed'
            });
            if (url) {
                jQuery(selector + ' iframe').attr("src", url);
            }
        });
        jQuery(selector).on('hidden.bs.modal', function () {
            /* scrolling issue in iOS 11.3*/
            jQuery('body').css({
                position: 'static'
            });
            var scrollT = jQuery(selector).data('scrollTop') || 0;
            if (scroll > 0) {
                jQuery(window).scrollTop(scrollT);
            }
        });
    }
    else {
        jQuery(selector).off('shown');
        jQuery(selector).on('shown', function () {
            /*jQuery(selector+' iframe').attr("src","about:blank");*/
            /* scrolling issue in iOS 11.3*/
            var scrollT = jQuery(window).scrollTop();
            if (scrollT > 0) {
                jQuery(selector).data('scrollTop', scrollT);
            }
            jQuery('body').css({
                position: 'fixed'
            });
            if (url) {
                jQuery(selector + ' iframe').attr("src", url);
            }
        });
        jQuery(selector).on('hidden', function () {
            /* scrolling issue in iOS 11.3*/
            jQuery('body').css({
                position: 'static'

            });
            var scrollT = jQuery(selector).data('scrollTop') || 0;
            if (scrollT > 0) {
                jQuery(window).scrollTop(scrollT);
            }
        });
    }
    jQuery(selector).modal({backdrop: true, show: true, keyboard: true, remote: ''});
    /** initialized with no keyboard */
    /** reloads parent page on close!
     ** jQuery(selector).on('hidden.bs.modal', function (e) { location.reload();}); */

}

function addEditModalHtml() {
    /** Will be true if bootstrap 3 is loaded, false if bootstrap 2 or no bootstrap */
    var bootstrap3_enabled = (typeof jQuery().emulateTransitionEnd == 'function');
    if (!jQuery("#myEditModal").length) {
        if (bootstrap3_enabled) {
            myEditModal = '<div class="jevbootstrap"><div class="modal   fade" id="myEditModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >'
                + '<div class="modal-dialog modal-lg">'
                + '<div class="modal-content">'
                + '<div class="modal-header">'
                + '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
                + '<h4 class="modal-title" id="myModalLabel"></h4>'
                + '</div>'
                + '<div class="modal-body">'
                + '<iframe src="about:blank;"></iframe>'
                + '</div>'
                + '</div>'
                + '</div>'
                + '</div>';
            +'</div>';
        }
        else {
            myEditModal = '<div class="jevbootstrap"><div class="modal  hide " id="myEditModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >'
                + '<div class="modal-dialog modal-lg">'
                + '<div class="modal-content">'
                + '<div class="modal-header">'
                + '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
                + '<h4 class="modal-title" id="myModalLabel"></h4>'
                + '</div>'
                + '<div class="modal-body">'
                + '<iframe src="about:blank;"></iframe>'
                + '</div>'
                + '</div>'
                + '</div>'
                + '</div>';
            +'</div>';
        }
        /** see http://stackoverflow.com/questions/10636667/bootstrap-modal-appearing-under-background */
        jQuery(myEditModal).appendTo("body");
    }
}

function jevImportPopup(url) {
    /** close dialog may not exist for monthly calendar */
    try {
        jQuery('.action_dialogJQ').modal('hide');
    }
    catch (e) {

    }
    addImportPopupHtml();

    /** see http://stackoverflow.com/questions/16152275/how-to-resize-twitter-bootstrap-modal-dynamically-based-on-the-content
     ** jQuery('#myImportModal .modal-body').css({  'top':'5px'});
     ** jQuery('#myImportModal .modal-header').css({  'display':'block'}); */

    launchModal('#myImportModal', url);


}


function addImportPopupHtml() {
    /** Will be true if bootstrap 3 is loaded, false if bootstrap 2 or no bootstrap */
    var bootstrap3_enabled = (typeof jQuery().emulateTransitionEnd == 'function');
    if (!document.getElementById("myImportModal")) {
        if (bootstrap3_enabled) {
            myImportModal = '<div class="jevbootstrap"><div class="modal  fade" id="myImportModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >'
                + '<div class="modal-dialog modal-sm">'
                + '<div class="modal-content">'
                + '<div class="modal-header">'
                + '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
                + '<h4 class="modal-title" id="exampleModalLabel">' + Joomla.JText._("JEV_IMPORT_ICALEVENT") + '</h4>'
                + '</div>'
                + '<div class="modal-body">'
                + '<iframe src="about:blank;"></iframe>'
                + '</div>'
                + '</div>'
                + '</div>'
                + '</div>';
            +'</div>';
        }
        else {
            myImportModal = '<div class="jevbootstrap"><div class="modal  hide fade" id="myImportModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >'
                + '<div class="modal-dialog modal-sm">'
                + '<div class="modal-content">'
                + '<div class="modal-header">'
                + '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
                + '<h4 class="modal-title" id="exampleModalLabel">' + Joomla.JText._("JEV_IMPORT_ICALEVENT") + '</h4>'
                + '</div>'
                + '<div class="modal-body">'
                + '<iframe src="about:blank;"></iframe>'
                + '</div>'
                + '</div>'
                + '</div>'
                + '</div>';
            +'</div>';
        }

        jQuery(myImportModal).appendTo("body");
    }
}

function jevIdPopup(id) {
    /** close dialog may not exist for monthly calendar */
    try {
        jQuery('#' + id).modal('hide');
    }
    catch (e) {

    }
    launchModal('#' + id);


}
