
function jevIdPopup(id) {
    /** close dialog may not exist for monthly calendar */
    try {
        jQuery('#' + id).modal('hide');
    }
    catch (e) {

    }
    launchJevModal('#' + id);
}

function jevModalSelector(sourceElement, params, evt) {
    if(sourceElement.getAttribute('data-jevmodal') || sourceElement.getAttribute('rel')) {
        evt.preventDefault();

        var id = 'jevModal' + Math.floor(Math.random() * Math.floor(100000));
        addJevModalHtml(id, sourceElement);

        var elementData = JSON.parse(sourceElement.getAttribute('data-jevmodal') || '{}');

        var modal = document.getElementById(id);
        var modalHeader = modal.querySelector('.modal-header ');
        var modalBody   = modal.querySelector('.modal-body ');
        var modalDialog = modal.querySelector('.modal-dialog ');
        var modalTitle  = modal.querySelector('.modal-title');
        var modalClose  = modal.querySelector('.modal-header .close');

        if (typeof elementData.size !== 'size') {
            modalDialog.classList.add(elementData.size);
        }

        if (typeof elementData.title !== 'undefined')
        {
            if (elementData.title !== "") {
                modalHeader.style.display = 'block';
            }
            modalTitle.innerHTML = elementData.title;
        }
        else
        {
            modalBody.style.top = '5px';

            if (modalClose) {
                modalClose.style.marginRight = '-15px';
                modalClose.style.marginTop = '-15px';
                modalClose.style.Opacity = 1;
                modalClose.style.fontSize = '30px';
            }
            modalHeader.style.height = '0px';
            modalHeader.style.zIndex = 99;
            modalHeader.style.border = 'none';

            modalTitle.style.display = 'none';
        }

        modal.style.maxHeight = '90%';

        var href = elementData.href  || sourceElement.href;
/*
        var iframe = document.querySelector('#' + id + ' iframe');
        iframe.addEventListener('load', function () {
            var iframe = document.querySelector('#' + id + ' iframe');
            if(iframe.src == href) {
                // add 20 to hide scroll bars that are not needed
                iframe.style.height = iframe.contentWindow.document.body.scrollHeight + 20 + 'px';
                window.setTimeout(function () {
                    var padding = parseInt(window.getComputedStyle(modalBody).getPropertyValue('padding-top'))
                        + parseInt(window.getComputedStyle(modalBody).getPropertyValue('padding-bottom'));
                    modalBody.style.maxHeight = (modal.offsetHeight - modalHeader.offsetHeight - padding) + 'px';
                    iframe.style.maxHeight = (modalBody.offsetHeight - padding) + 'px';

                }, 100);
            }
        });
*/
        launchJevModal('#' + id, href);
    }
    else
    {
        return;
    }

}

function jevModalResize(id) {

    var modal = document.getElementById(id);
    var modalHeader = modal.querySelector('.modal-header ');
    var modalBody   = modal.querySelector('.modal-body ');
    var modalDialog = modal.querySelector('.modal-dialog ');
    var modalTitle  = modal.querySelector('.modal-title');
    var modalClose  = modal.querySelector('.modal-header .close');

    var elementData = JSON.parse(modal.getAttribute('data-jevmodal') || '{}');

    if (typeof elementData.size !== 'size') {
        modalDialog.classList.add(elementData.size);
    }

    if (typeof elementData.title !== 'undefined')
    {
        if (elementData.title !== "") {
            modalHeader.style.display = 'block';
        }
        modalTitle.innerHTML = elementData.title;
    }
    else
    {
        modalBody.style.top = '5px';

        if (modalClose) {
            modalClose.style.marginRight = '-15px';
            modalClose.style.marginTop = '-15px';
            modalClose.style.Opacity =  1;
            modalClose.style.fontSize = '30px';
        }

        modalHeader.style.height = '0px';
        modalHeader.style.zIndex = 99;
        modalHeader.style.border = 'none';

        modalTitle.style.display = 'none';
    }

    modal.style.maxHeight = '90%';

}

function jevModalPopup(id, url, title) {
    addJevModalHtml(id);

    // see http://stackoverflow.com/questions/16152275/how-to-resize-twitter-bootstrap-modal-dynamically-based-on-the-content
    //jQuery('#' + id + ' .modal-header').css({'display': 'block'});
    jQuery('#' + id + ' .modal-title').html(title)
    launchJevModal('#' + id, url);

    return;
}

function jevModalNoHeader(id, url) {
    addJevModalHtml(id);

    // see http://stackoverflow.com/questions/16152275/how-to-resize-twitter-bootstrap-modal-dynamically-based-on-the-content
    jQuery('#' + id + ' .modal-body').css({'top': '5px'});
    jQuery('#' + id + ' .modal-header').css({'display': 'none'});
    launchJevModal('#' + id, url);
    return;
}

function jevModalNoTitle(id, url) {
    addJevModalHtml(id);

    // see http://stackoverflow.com/questions/16152275/how-to-resize-twitter-bootstrap-modal-dynamically-based-on-the-content
    jQuery('#' + id + ' .modal-body').css({'top': '5px'});
    jQuery('#' + id + ' .modal-header .close').css({
        'margin-right': '-15px',
        'margin-top': '-15px',
        'opacity': 1,
        'font-size:': '30px'
    });
    jQuery('#' + id + ' .modal-header ').css({'height': '0px', 'z-index': '99', 'border': 'none'});
    jQuery('#' + id + ' .modal-header .modal-title').css({'display': 'none'});

    launchJevModal('#' + id, url);
    return;
}

function launchJevModal(selector, url) {
    // Clear the old page!
    var iframe = document.querySelector(selector + ' iframe');
    if (iframe) {
        iframe.src = "";
        iframe.addEventListener('load', function () {
            var iframe = document.querySelector(selector + ' iframe');
            if (iframe.src.indexOf(url) >= 0) {

                var modal = document.querySelector(selector);
                var modalHeader = modal.querySelector('.modal-header ');
                var modalBody = modal.querySelector('.modal-body ');
                var modalContent = modal.querySelector('.modal-content ');
                var modalDialog = modal.querySelector('.modal-dialog ');

                window.addEventListener('resize', function () {
                    jevIframeSizing(iframe, modal, modalHeader, modalBody, modalContent, modalDialog);
                })

                window.setTimeout(function () {
                    jevIframeSizing(iframe, modal, modalHeader, modalBody, modalContent, modalDialog);
                }, 500);
            }
        });
    }

    jQuery(selector).off('show shown.bs.modal');
    jQuery(selector).on('show shown.bs.modal', function () {
        var modal = document.querySelector(selector);
        if (modal.classList.contains('fade'))
        {
            modal.classList.remove('fade');
        }

        //jQuery(selector+' iframe').attr("src","about:blank");
        // scrolling issue in iOS 11.3
        var scrollT = jQuery(window).scrollTop();
        if (scrollT > 0) {
            jQuery(selector).data('scrollTop', scrollT);
        }
        jQuery('body').css({
          //  position: 'fixed'
        });
        if (url) {
            jQuery(selector + ' iframe').attr("src", url);
        }
    });
    jQuery(selector).on('hide hidden.bs.modal', function () {
        // scrolling issue in iOS 11.3
        jQuery('body').css({
          //  position: 'static'
        });
        var scrollT = jQuery(selector).data('scrollTop') || 0;
        if (scrollT > 0) {
            jQuery(window).scrollTop(scrollT);
        }
    });

    // Joomla 4/Bootstrap 5 changes
    var bootstrap5 = false;
    var bootstrap4 = false;
    try {
        var testClass = window.bootstrap.Tooltip || window.bootstrap.Modal;
        var bsVersion = testClass.VERSION.substr(0,1);
        bootstrap5 = bsVersion >= 5;
        bootstrap4 = bsVersion >= 4 && !bootstrap5;
    } catch (e) {
    }

    if (bootstrap5)
    {
        var myModal = new bootstrap.Modal(document.querySelector(selector), {backdrop: true, show: true, keyboard: true, remote: ''});
        myModal.show();
    }
    else {
        jQuery(selector).modal({backdrop: true, show: true, keyboard: true, remote: ''}) // initialized with no keyboard
    }

    return;
}

function jevIframeSizing(iframe, modal, modalHeader, modalBody, modalContent, modalDialog) {
    if (!iframe)
    {
        return;
    }
    // add 20 to hide scroll bars that are not needed
    // console.log("width = " + iframe.contentDocument.body.scrollWidth + " vs " + iframe.contentDocument.body.offsetWidth);
    // console.log("height = " + iframe.contentDocument.body.scrollHeight + " vs " + iframe.contentDocument.body.offsetHeight);
    var extraHeight = (iframe.contentDocument.body.scrollHeight > iframe.contentDocument.body.offsetHeight) ? 20 : 0;
    // if extraheight is 20 then there will be a scroll bar visible
    var extraWidth = (iframe.contentDocument.body.scrollWidth > iframe.contentDocument.body.offsetWidth || extraHeight == 20) ? 20 : 0;
    //console.log('iframe ' + extraHeight + " : " + extraWidth);
    //console.log('set iframe Height = ' + (iframe.contentDocument.body.scrollHeight + extraHeight) + 'px');
    //console.log('set iframe Width  = ' + (iframe.contentDocument.body.scrollWidth  + extraWidth) + 'px');

    iframe.style.height = (iframe.contentDocument.body.scrollHeight + extraHeight) + 'px';
    iframe.style.width  = (iframe.contentDocument.body.scrollWidth  + extraWidth) + 'px';

    if(modalBody.offsetWidth > iframe.contentDocument.body.scrollWidth  + extraWidth)
    {
        iframe.style.width = (modalBody.offsetWidth - 20) + 'px';
    }

    /*
    var padding = parseInt(window.getComputedStyle(modalBody).getPropertyValue('padding-top'))
        + parseInt(window.getComputedStyle(modalBody).getPropertyValue('padding-bottom'));
    modalBody.style.maxHeight = (modal.offsetHeight - modalHeader.offsetHeight - padding) + 'px';
    iframe.style.maxHeight = (modalBody.offsetHeight - padding) + 'px';
*/
}

function addJevModalHtml(id) {
    /** Will be true if bootstrap 3 is loaded, false if bootstrap 2 or no bootstrap */
    var bootstrap5 = false;
    var bootstrap3_enabled = (typeof jQuery().emulateTransitionEnd == 'function');
    if (!bootstrap3_enabled) {
        try {
            var testClass = window.bootstrap.Tooltip || window.bootstrap.Modal;
            var bsVersion = testClass.VERSION.substr(0,1);

            bootstrap3_enabled = bsVersion >= 4;
            bootstrap5 = bsVersion >= 5;
        } catch (e) {
        }
    }

    var myModal = "";
    var modalsize = 'jevmodal-full';
    if (!document.getElementById(id)) {
        if (bootstrap5) {
            myModal = '<div class="modal  ' + modalsize + ' jevmodal" id="' + id + '" tabindex="-1" role="dialog" aria-labelledby="' + id + 'Label" aria-hidden="true" >'
                + '<div class="modal-dialog modal-lg modal-xl modal-dialog-centered">'
                + '<div class="modal-content">'
                + '<div class="modal-header">'
                + '<h4 class="modal-title" id="' + id + 'Label"></h4>'
                + '<button type="button" class="btn-close uk-modal-close-default" data-bs-dismiss="modal" aria-label="Close"></button>'
                + '</div>'
                + '<div class="modal-body">'
                + '<iframe src="" ></iframe>'
                + '</div>'
                + '</div>'
                + '</div>'
                + '</div>';
        }
        else if (bootstrap3_enabled) {
            myModal = '<div class="modal   fade ' + modalsize + ' jevmodal" id="' + id + '" tabindex="-1" role="dialog" aria-labelledby="' + id + 'Label" aria-hidden="true" >'
                + '<div class="modal-dialog modal-lg modal-xl modal-dialog-centered">'
                + '<div class="modal-content">'
                + '<div class="modal-header">'
                + '<button type="button" class="close uk-modal-close-default" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
                + '<h4 class="modal-title" id="' + id + 'Label"></h4>'
                + '</div>'
                + '<div class="modal-body">'
                + '<iframe src="" ></iframe>'
                + '</div>'
                + '</div>'
                + '</div>'
                + '</div>';
        }
        else {
            myModal = '<div class="modal  hide fade ' + modalsize + ' jevmodal" id="' + id + '" tabindex="-1" role="dialog" aria-labelledby="' + id + 'Label" aria-hidden="true" >'
                + '<div class="modal-dialog ">'
                + '<div class="modal-content">'
                + '<div class="modal-header">'
                + '<button type="button" class="close uk-modal-close-default" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
                + '<h4 class="modal-title" id="' + id + 'Label"></h4>'
                + '</div>'
                + '<div class="modal-body">'
                + '<iframe src=""></iframe>'
                + '</div>'
                + '</div>'
                + '</div>'
                + '</div>';
        }
        // see http://stackoverflow.com/questions/10636667/bootstrap-modal-appearing-under-background
        jQuery(myModal).appendTo("body");
    }
}

// Polyfills for MSIE
if (window.NodeList && !NodeList.prototype.forEach) {
    NodeList.prototype.forEach = Array.prototype.forEach;
}

function closeJevModalBySelector(selector)
{
    // Joomla 4/Bootstrap 5 changes
    var bootstrap5 = false;
    var bootstrap4 = false;
    try {
        var testClass = window.bootstrap.Tooltip || window.bootstrap.Modal;
        var bsVersion = testClass.VERSION.substr(0,1);

        bootstrap5 = bsVersion >= 5;
        bootstrap4 = bsVersion >= 4 && !bootstrap5;
    } catch (e) {
    }

    if (bootstrap5)
    {
        var myModalEls = document.querySelectorAll(selector)
        myModalEls.forEach(function (myModalEl) {
            var modal = bootstrap.Modal.getInstance(myModalEl)
            modal.hide();
        });
    }
    else {
        var $selector = jQuery(selector);
         $selector.modal('hide');
    }
}