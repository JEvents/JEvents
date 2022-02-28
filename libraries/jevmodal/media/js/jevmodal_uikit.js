function jevModalPopup(id, url, title) {
    addJevModalHtml_uikit(id);

    // see http://stackoverflow.com/questions/16152275/how-to-resize-twitter-bootstrap-modal-dynamically-based-on-the-content
    document.querySelector('#' + id + ' .uk-modal-title').style.display= 'block';
    document.querySelector('#' + id + ' .uk-modal-title').innerHTML = title;
    
    launchJevModal_uikit('#' + id, url);

    return;
}

function jevModalPopupOpen(id)
{
    launchJevModal_uikit('#' + id);

    return;
}

function jevModalNoHeader(id, url) {
    addJevModalHtml_uikit(id);

    document.querySelector('#' + id + ' .uk-modal-body').style.top = '5px';
    document.querySelector('#' + id + ' .uk-modal-title').style.display = 'none';
    launchJevModal_uikit('#' + id, url);
    return;
}

function jevModalNoTitle(id, url) {
    addJevModalHtml_uikit(id);

    document.querySelector('#' + id + ' .uk-modal-title').style.display= 'none';

    launchJevModal_uikit('#' + id, url);
    return;
}

function launchJevModal_uikit(selector, url = false) {
    // Clear the old page!
    var iframe = document.querySelector(selector + ' iframe');
    if (iframe && url) {
        document.querySelector(selector + ' iframe').src = "";
        UIkit.util.on(selector, 'show', function () {
            if (url) {
                document.querySelector(selector + ' iframe').src = url;
            }
        });
    }

    UIkit.util.on(selector, 'hide', function () {
    });

    UIkit.modal(selector).show();

    return;
}

function addJevModalHtml(id, withiframe = true, modalData = {}) {
    addJevModalHtml_uikit(id, withiframe, modalData) ;
}

function addJevModalHtml_uikit(id, withiframe = true, modalData = {}) {
    var myModal = "";
    var title = "";
    var body = "";
    var modalsize = "uk-modal-container jevmodal-full";
    var modalheight = "uk-height-1-1";

    if (modalData)
    {
        title = modalData.title;
        body  = modalData.body;
        modalsize = '';
        modalheight = '';
    }

    if (!document.getElementById(id)) {
        myModal = '<div uk-modal="bg-close: false, stack:true" class="' + modalsize + ' jevmodal" id="' + id + '" tabindex="-1" role="dialog" aria-labelledby="' + id + 'Label" aria-hidden="true" >'
            + '<div class="uk-modal-dialog uk-modal-body ' + modalheight + '">'
            + '<div class="uk-modal-header">'
            + '<button type="button" class="uk-modal-close-default" uk-close aria-label="Close"></button>'
            + '<h4 class="uk-modal-title" id="' + id + 'Label">' + title + '</h4>'
            + '</div>'
            + '<div class="uk-modal-body">'
            + (withiframe ? '<iframe src="//about:blank;" class="uk-width-expand " style="height: calc(100% - 75px);"></iframe>' : '')
            + body
            + '</div>'
            + '</div>'
            + '</div>';

        // see http://stackoverflow.com/questions/10636667/bootstrap-modal-appearing-under-background
        if (document.getElementById('gslc')) {
            document.getElementById('gslc').insertAdjacentHTML('beforeend', myModal);
        }
        else
        {
            document.getElementById('jevents').insertAdjacentHTML('beforeend', myModal);
        }
    }
}

function closeJevModalBySelector(selector)
{
    document.querySelectorAll(selector).forEach(function(modalItem)  {
        UIkit.modal(modalItem).hide();
    });
}