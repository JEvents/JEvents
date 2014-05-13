

var jevjq;
function checkJQ() {
    //alert(typeof $);
    if (window.jQuery && jQuery.fn) {
        jevjq = jQuery.noConflict();
    }
}
checkJQ();

// Useful extensions to jQuery
 // see http://davidwalsh.name/jquery-add-event

if (typeof jevjq.fn.addEvent == "undefined"){
	jevjq.fn.addEvent = jevjq.fn.bind;
} 

/*
 * var jevjq = {};

 function checkJQ() {
 alert("window.jQuery "+window.jQuery);
 alert("window.$ "+window.$);
 alert("Mootools $ "+$);
 alert("jQuery.fn "+jQuery.fn);
 alert("jQuery.fn.jquery "+jQuery.fn.jquery);
 return window.jQuery && jQuery.fn && /^1\.[3-9]/.test(jQuery.fn.jquery);
 }

 jevjq.jQuery = jQuery.noConflict();
 */


/*
function dynamicallyLoadJQuery(){
    if (window.jQuery && jQuery.fn) {
        jevjq = jQuery.noConflict();
    }
    else {
        // not loaded to load dymaimcally
        google.load("jquery", "1.10.2", {"callback" :checkJQ });
        jevjq = jQuery.noConflict();
    }    
}
*/

// See http://stackoverflow.com/questions/10113366/load-jquery-with-javascript-and-use-jquery
// and https://developers.google.com/loader/
// Note the dyamic load doesn't seem to work with jQuery 1.10.2 for some reason

// Thanks to http://css-tricks.com/snippets/jquery/load-jquery-only-if-not-present/
/*
function dynamicJQueryLoader() {
// Only do anything if jQuery isn't defined
    if (typeof jQuery == 'undefined') {

        if (typeof $ == 'function') {
            // warning, global var
            thisPageUsingOtherJSLibrary = true;
        }

        function getScript(url, success) {

            var script = document.createElement('script');
            script.src = url;

            var head = document.getElementsByTagName('head')[0];
            var done = false;

            // Attach handlers for all browsers
            script.onload = script.onreadystatechange = function() {

                if (!done && (!this.readyState || this.readyState == 'loaded' || this.readyState == 'complete')) {
                    done = true;

                    // callback function provided as param
                    success();

                    script.onload = script.onreadystatechange = null;
                    // no need to remove this script surely!
                    //head.removeChild(script);

                }
            }
            head.appendChild(script);
        }

        getScript('//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js', function() {

            if (typeof jQuery == 'undefined') {
                // Super failsafe - still somehow failed...
            } else {
                // Use .noConflict(), then run your jQuery Code
                checkJQ();
            }
        });

    } 
    else { // jQuery was already loaded
        // Run your jQuery Cod
        checkJQ();
    }
}
*/
// Also see http://stackoverflow.com/questions/10113366/load-jquery-with-javascript-and-use-jquery
// and https://developers.google.com/loader/