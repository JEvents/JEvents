function callNavigation(link, datatype) {
    link += "&json=1";
    //link += "&XDEBUG_SESSION_START=netbeans-xdebug";
    var jSonRequest = jQuery.ajax({
        type: 'GET',
        // use JSONP to allow cross-domain calling of this data!
        dataType: (typeof datatype !== 'undefined') ? datatype : 'jsonp',
        cache: false,
        url: link,
        contentType: "application/json; charset=utf-8",
        scriptCharset: "utf-8"
    })
        .done(function (json) {
            if (!json || !json.data) {
                if (typeof datatype == 'undefined') {
                    return callNavigation(link, "json");
                }
                alert('could not get calendar');
                return;
            }
            if (json.script) {
                //alert(json.script);
                var script = JSON.parse(json.script);
                try {
                    linkprevious = script.linkprevious;
                    linknext = script.linknext;
                }
                catch (e)
                {
                    // fallback to old version
                    eval(json.script);
                }
            }
            var myspan = document.getElementById("testspan" + json.modid);
            var modbody = myspan.parentNode;
            modbody.innerHTML = json.data;
            // we may have tooltips to re-enable too!
            try {
                if (typeof jevPopover == "function")
                {
                    jevPopover('.hasjevtip', modbody);
                }
                else {
                    // Joomla 4/Bootstrap 5 changes
                    var bootstrap5 = false;
                    var bootstrap4 = false;
                    try {
                        var bsVersion = window.bootstrap.Tooltip.VERSION.substr(0,1);
                        bootstrap5 = bsVersion >= 5;
                        bootstrap4 = bsVersion >= 4 && !bootstrap5;
                    } catch (e) {
                    }
                    popoveroptions = {
                        'html': true,
                        'placement': 'top',
                        'title': '',
                        'trigger': 'hover focus',
                        'content': '',
                        'delay': {'hide': 150, 'show': 150},
                        'container': modbody
                    };
                    if (bootstrap5)
                    {
                        modbody.querySelectorAll('.hasjevtip').forEach(function (el) {
                            var pop = new window.bootstrap.Popover(el, popoveroptions);
                        });
                    }
                    else {
                        jQuery(modbody).find('.hasjevtip').popover(popoveroptions);
                    }
                }
            }
            catch (e) {
            }

            // we may have popup links too
            try {
                setupEventPopups();
            }
            catch (e) {
            }

            setupSpecificNavigation();

        })
        .fail(function (jqxhr, textStatus, error) {
            alert(textStatus + ", " + error);
        });
}

// setup touch interaction
jQuery(document).on('ready', function () {
    setupSpecificNavigation();
});

var jevMiniTouchStartX = false;
var jevMiniTouchStartY = false;

function setupMiniCalTouchInteractions(selector, parent) {
    if ('ontouchstart' in document.documentElement) {
        var target = parent ? jQuery(selector).parent() : jQuery(selector);
        target.on("touchend", function (evt) {
            //jevlog("touchend/touchend.");
            //jevlog('changed touches '+ evt.originalEvent.changedTouches.length);
            var touchobj = evt.originalEvent.changedTouches[0];
            var vdist = touchobj.pageY - jevMiniTouchStartY;
            if (Math.abs(vdist) > 50) {
                evt.preventDefault();
                return;
            }
            var distX = touchobj.pageX - jevMiniTouchStartX;
            if (distX > 10) {
                if (linkprevious) callNavigation(linkprevious);
                evt.preventDefault();
            }
            else if (distX < -10) {
                if (linknext) callNavigation(linknext);
                evt.preventDefault();
            }
        });
        target.on("touchstart", function (evt) {
            //evt.preventDefault();
            var touchobj = evt.originalEvent.changedTouches[0];
            jevMiniTouchStartX = touchobj.pageX;
            jevMiniTouchStartY = touchobj.pageY;
        });
        target.on("touchmove", function (evt) {
            // top stop scrolling etc. but only in the horizontal
            var touchobj = evt.originalEvent.changedTouches[0];

            var distX = touchobj.pageX - jevMiniTouchStartX;
            //jevlog(distX + " "+Math.abs(distX));
            if (Math.abs(distX) > 5) {
                evt.preventDefault();

            }
            /*
             var vdist = touchobj.pageY - jevMiniTouchStartY ;
             jevlog(vdist + " "+Math.abs(vdist));
             if (Math.abs(vdist)<20) {
                evt.preventDefault();
            }
            */
        });
    }
}

function jevlog(msg) {
    try {
        console.log(msg);
    }
    catch (e) {

    }

}
