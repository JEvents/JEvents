function jevModalPopup(id, url, title) {
    addJevModalHtml_gsl(id);

    // see http://stackoverflow.com/questions/16152275/how-to-resize-twitter-bootstrap-modal-dynamically-based-on-the-content
    document.querySelector('#' + id + ' .gsl-modal-title').style.display= 'block';
    document.querySelector('#' + id + ' .gsl-modal-title').innerHTML = title;
    
    launchJevModal_gsl('#' + id, url);

    return;
}

function jevModalNoHeader(id, url) {
    addJevModalHtml_gsl(id);

    document.querySelector('#' + id + ' .gsl-modal-body').style.top = '5px';
    document.querySelector('#' + id + ' .gsl-modal-title').style.display = 'none';
    launchJevModal_gsl('#' + id, url);
    return;
}

function jevModalNoTitle(id, url) {
    addJevModalHtml_gsl(id);

    document.querySelector('#' + id + ' .gsl-modal-title').style.display= 'none';

    launchJevModal_gsl('#' + id, url);
    return;
}

function launchJevModal_gsl(selector, url) {
    // Clear the old page!
    var iframe = document.querySelector(selector + ' iframe');
    if (iframe) {
        document.querySelector(selector + ' iframe').src = "";
        gslUIkit.util.on(selector, 'show', function () {
            if (url) {
                document.querySelector(selector + ' iframe').src = url;
            }
        });
    }

    gslUIkit.util.on(selector, 'hide', function () {
    });

    gslUIkit.modal(selector).show();

    return;
}

function addJevModalHtml(id, withiframe = true, modalData = {}) {
    addJevModalHtml_gsl(id, withiframe, modalData) ;
}

function addJevModalHtml_gsl(id, withiframe = true, modalData = {}) {
    var myModal = "";
    var title = "";
    var body = "";

    if (modalData)
    {
        title = modalData.title;
        body = modalData.body;
    }

    var modalsize = 'jevmodal-full';
    if (!document.getElementById(id)) {
        myModal = '<div gsl-modal="bg-close: false, stack:true" class="gsl-modal-container ' + modalsize + ' jevmodal" id="' + id + '" tabindex="-1" role="dialog" aria-labelledby="' + id + 'Label" aria-hidden="true" >'
            + '<div class="gsl-modal-dialog gsl-modal-body gsl-height-1-1">'
            + '<div class="gsl-modal-header">'
            + '<button type="button" class="gsl-modal-close-default" gsl-close aria-label="Close"></button>'
            + '<h4 class="gsl-modal-title" id="' + id + 'Label">' + title + '</h4>'
            + '</div>'
            + '<div class="gsl-modal-body">'
            + (withiframe ? '<iframe src="//about:blank;" class="gsl-width-expand " style="height: calc(100% - 75px);"></iframe>' : '')
            + body
            + '</div>'
            + '</div>'
            + '</div>';

        // see http://stackoverflow.com/questions/10636667/bootstrap-modal-appearing-under-background
        document.getElementById('gslc').insertAdjacentHTML('beforeend', myModal);
    }
}

function closeJevModalBySelector(selector)
{
    document.querySelectorAll(selector).forEach(function(modalItem)  {
        gslUIkit.modal(modalItem).hide();
    });
}