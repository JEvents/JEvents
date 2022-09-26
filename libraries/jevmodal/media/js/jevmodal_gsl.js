function jevIdPopup(id) {
    /** close dialog may not exist for monthly calendar */
    try {
        jQuery('#' + id).modal('hide');
    }
    catch (e) {

    }
    launchJevModal_gsl('#' + id);
}

function jevModalSelector(sourceElement, params, evt) {
    if(sourceElement.getAttribute('data-jevmodal') || sourceElement.getAttribute('rel')) {
        evt.preventDefault();

        var id = 'jevModal' + Math.floor(Math.random() * Math.floor(100000));
        addJevModalHtml_gsl(id, sourceElement);

        var elementData = JSON.parse(sourceElement.getAttribute('data-jevmodal') || '{}');

        var modal = document.getElementById(id);
        var modalBody   = modal.querySelector('.gsl-modal-body');
        var modalDialog = modal.querySelector('.gsl-modal-dialog ');
        var modalTitle  = modal.querySelector('.gsl-modal-title');
        var modalClose  = modal.querySelector('.gsl-close');

        if (typeof elementData.size !== 'size') {
            modalDialog.classList.add(elementData.size);
        }

        if (typeof elementData.title !== 'undefined')
        {
            if (elementData.title !== "") {
                modalTitle.style.display = 'block';
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
            modalTitle.style.display = 'none';
        }

        modal.style.maxHeight = '90%';

        var href = elementData.href  || sourceElement.href;
        launchJevModal_gsl('#' + id, href);
    }
    else
    {
        return;
    }

}

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

function addJevModalHtml_gsl(id) {
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
        var container = document.getElementById('gslc') || document.getElementById('jevents')  || document.getElementById('myTab');
        container.insertAdjacentHTML('beforeend', myModal);

    }
}

function closeJevModalBySelector(selector)
{
    document.querySelectorAll(selector).forEach(function(modalItem)  {
        gslUIkit.modal(modalItem).hide();
    });
}