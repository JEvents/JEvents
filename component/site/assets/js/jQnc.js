

var jevjq;
function checkJQ() {
    //alert(typeof $);
    if (window.jQuery && jQuery.fn) {
        jevjq = jQuery.noConflict();
    }
}
checkJQ();

// Will be true if bootstrap 3 is loaded, false if bootstrap 2 or no bootstrap
var bootstrap3_enabled = (typeof jQuery().emulateTransitionEnd == 'function');

// workaround for tooltips and popovers failing when MooTools is enabled with Bootstrap 3
// See http://www.mintjoomla.com/support/community-forum/user-item/1833-braza/48-cobalt-8/2429.html?start=20
if(window.MooTools && bootstrap3_enabled) {
	var mHide = Element.prototype.hide;
	var mSlide = Element.prototype.slide;

	Element.implement({


		hide: function () {
			if (this.is("[rel=tooltip]")) {
				return this;
			}
			mHide.apply(this, arguments);
		},


		slide: function (v) {
			if (this.hasClass("carousel")) {
				return this;
			}
			mSlide.apply(this, v);
		}
	});
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