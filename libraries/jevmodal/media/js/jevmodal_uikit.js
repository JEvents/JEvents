function jevIdPopup(id) {
    /** close dialog may not exist for monthly calendar */
    try {
        jQuery('#' + id).modal('hide');
    }
    catch (e) {

    }
    launchJevModal_uikit('#' + id);
}

function jevModalSelector(sourceElement, params, evt) {
    if(sourceElement.getAttribute('data-jevmodal') || sourceElement.getAttribute('rel')) {
        evt.preventDefault();

        var id = 'jevModal' + Math.floor(Math.random() * Math.floor(100000));
        addJevModalHtml_uikit(id, sourceElement);

        var elementData = JSON.parse(sourceElement.getAttribute('data-jevmodal') || '{}');

        var modal = document.getElementById(id);
        var modalBody   = modal.querySelector('.uk-modal-body');
        var modalDialog = modal.querySelector('.uk-modal-dialog ');
        var modalTitle  = modal.querySelector('.uk-modal-title');
        var modalClose  = modal.querySelector('.uk-close');

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
        launchJevModal_uikit('#' + id, href);
    }
    else
    {
        return;
    }

}



function jevModalPopup(id, url, title) {
    addJevModalHtml_uikit(id);

    // see http://stackoverflow.com/questions/16152275/how-to-resize-twitter-bootstrap-modal-dynamically-based-on-the-content
    document.querySelector('#' + id + ' .uk-modal-title').style.display= 'block';
    document.querySelector('#' + id + ' .uk-modal-title').innerHTML = title;
    
    launchJevModal_uikit('#' + id, url);

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

function launchJevModal_uikit(selector, url) {
    // Clear the old page!
    var iframe = document.querySelector(selector + ' iframe');
    if (iframe) {
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

function addJevModalHtml_uikit(id) {
    var myModal = "";
    var modalsize = '';
    if (!document.getElementById(id)) {
        myModal = '<div uk-modal="bg-close: false, stack:true" class="uk-modal-container ' + modalsize + ' jevmodal" id="' + id + '" tabindex="-1" role="dialog" aria-labelledby="' + id + 'Label" aria-hidden="true" >'
            + '<div class="uk-modal-dialog uk-modal-body uk-height-1-1">'
            + '<h4 class="uk-modal-title" id="' + id + 'Label"></h4>'
            + '<button type="button" class="uk-modal-close-default" uk-close aria-label="Close"></button>'
            + '<iframe src="//about:blank;" class="uk-width-expand " style="height: calc(100% - 75px);"></iframe>'
            + '</div>'
            + '</div>';

        // see http://stackoverflow.com/questions/10636667/bootstrap-modal-appearing-under-background
        var container = document.getElementById('gslc') || document.getElementById('jevents');
        container.insertAdjacentHTML('beforeend', myModal);
    }
}

function closeJevModalBySelector(selector)
{
    document.querySelectorAll(selector).forEach(function(modalItem)  {
        UIkit.modal(modalItem).hide();
    });
}