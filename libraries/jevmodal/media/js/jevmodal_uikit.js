function jevModalPopup(id, url, title) {
    addJevModalHtml_uikit(id);

    // see http://stackoverflow.com/questions/16152275/how-to-resize-twitter-bootstrap-modal-dynamically-based-on-the-content
    document.querySelector('#' + id + ' .gsl-modal-title').style.display= 'block';
    document.querySelector('#' + id + ' .gsl-modal-title').innerHTML = title;
    
    launchJevModal_uikit('#' + id, url);

    return;
}

function jevModalNoHeader(id, url) {
    addJevModalHtml_uikit(id);

    document.querySelector('#' + id + ' .gsl-modal-body').style.top = '5px';
    document.querySelector('#' + id + ' .gsl-modal-title').style.display = 'none';
    launchJevModal_uikit('#' + id, url);
    return;
}

function jevModalNoTitle(id, url) {
    addJevModalHtml_uikit(id);

    document.querySelector('#' + id + ' .gsl-modal-title').style.display= 'none';

    launchJevModal_uikit('#' + id, url);
    return;
}

function launchJevModal_uikit(selector, url) {
    // Clear the old page!
    document.querySelector(selector + ' iframe').src =  "";
    gslUIkit.util.on(selector, 'show', function () {
        if (url) {
            document.querySelector(selector + ' iframe').src = url;
        }
    });


    gslUIkit.util.on(selector, 'hide', function () {
    });

    gslUIkit.modal(selector).show();

    return;
}

function addJevModalHtml_uikit(id) {
    var myModal = "";
    var modalsize = 'jevmodal-full';
    if (!document.getElementById(id)) {
        myModal = '<div gsl-modal="bg-close: false, stack:true" class="gsl-modal-container ' + modalsize + ' jevmodal" id="' + id + '" tabindex="-1" role="dialog" aria-labelledby="' + id + 'Label" aria-hidden="true" >'
            + '<div class="gsl-modal-dialog gsl-modal-body gsl-height-1-1">'
            + '<h4 class="gsl-modal-title" id="' + id + 'Label"></h4>'
            + '<button type="button" class="gsl-modal-close-default" gsl-close aria-label="Close"></button>'
            + '<iframe src="//about:blank;" class="gsl-width-expand " style="height: calc(100% - 75px);"></iframe>'
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