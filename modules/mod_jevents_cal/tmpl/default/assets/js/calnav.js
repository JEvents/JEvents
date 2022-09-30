function callNavigation(link, datatype) {
    link += "&json=1";

    var xhr = new XMLHttpRequest();
    xhr.open("GET", link);
    xhr.setRequestHeader("accept", "application/json; charset=utf-8")
    xhr.onload = function () {
        try {
            if (xhr.status != 200) {
                alert('error ' + xhr.statusText);
                return;
            }

            var json = JSON.parse(xhr.responseText);
            if (!json || !json.data) {
                if (typeof datatype == 'undefined') {
                    return callNavigation(link, "json");
                }
                alert('could not get calendar');
                return;
            }
            if (json.script) {
                //alert(json.script);
                try {
                    var script = JSON.parse(json.script);
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
                        var testClass = window.bootstrap.Tooltip || window.bootstrap.Modal;
                        var bsVersion = testClass.VERSION.substr(0,1);
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
                        if (typeof jQuery !== 'undefined') {
                            jQuery(modbody).find('.hasjevtip').popover(popoveroptions);
                        }
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

        } catch (e) {
            alert('bad response = ' + e + "\n" + xhr.responseText );
        }
    }

    xhr.onerror = function () {
        alert('error ' + xhr.statusText);

    }

    xhr.send();

}

// setup touch interaction
document.addEventListener('DOMContentLoaded', function () {
    setupSpecificNavigation();
});

var jevMiniTouchStartX = false;
var jevMiniTouchStartY = false;

function setupMiniCalTouchInteractions(selector, parent) {
    var target = parent ? document.querySelector(selector).parentNode : selector;
    if ('ontouchstart' in document.documentElement) {
        var target = parent ? document.querySelector(selector).parentNode : selector;
        target.addEventListener("touchend", function (evt) {
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
        target.addEventListener("touchstart", function (evt) {
            //evt.preventDefault();
            var touchobj = evt.originalEvent.changedTouches[0];
            jevMiniTouchStartX = touchobj.pageX;
            jevMiniTouchStartY = touchobj.pageY;
        });
        target.addEventListener("touchmove", function (evt) {
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
