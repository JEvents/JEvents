var jevjq;
function checkJQ() {
    //alert(typeof $);
    if (window.jQuery && jQuery.fn) {
        jevjq = jQuery.noConflict();
    }
}
checkJQ();

// workaround for tooltips and popovers failing when MooTools is enabled with Bootstrap 3
// See http://www.mintjoomla.com/support/community-forum/user-item/1833-braza/48-cobalt-8/2429.html?start=20

jQuery(document).on('ready', function() {
	// Will be true if bootstrap 3 is loaded, false if bootstrap 2 or no bootstrap
	var bootstrap3_enabled = (typeof jQuery().emulateTransitionEnd == 'function');

	if(window.MooTools && bootstrap3_enabled) {
		/**
		 * Workaround based on the code written by JoomlaArt
		 *
		 * @copyright     Copyright (C) 2004-2013 JoomlArt.com. All Rights Reserved.
		 * @license       GNU General Public License version 2 or later; see LICENSE.txt
		 * @authors       JoomlArt, JoomlaBamboo, (contribute to this project at github
		 *                & Google group to become co-author)
		 *------------------------------------------------------------------------------
		 */

		var mtHide = Element.prototype.hide,
			mtShow = Element.prototype.show,
			mtSlide = Element.prototype.slide;

		Element.implement({
			show: function(args){
				if(arguments.callee &&
					arguments.callee.caller &&
					arguments.callee.caller.toString().indexOf('isPropagationStopped') !== -1){	//jquery mark
					return this;
				}
				return jQuery.isFunction(mtShow) && mtShow.apply(this, args);
			},

			hide: function(){
				if(arguments.callee &&
					arguments.callee.caller &&
					arguments.callee.caller.toString().indexOf('isPropagationStopped') !== -1){	//jquery mark
					return this;
				}
				return jQuery.isFunction(mtHide) && mtHide.apply(this, arguments);
			},

			slide: function(args){
				if(arguments.callee &&
					arguments.callee.caller &&
					arguments.callee.caller.toString().indexOf('isPropagationStopped') !== -1){	//jquery mark
					return this;
				}
				return jQuery.isFunction(mtSlide) && mtSlide.apply(this, args);
			}
		});
	}
});

// disable click event in popover for non-touch devices
jQuery(document).on('ready', function() {
	 if (!('ontouchstart' in document.documentElement)) {
		//alert('non-touch');
		jQuery("<style>" ).prop("type" , "text/css").html(" .jev-click-to-open {display:none;}").appendTo("head");
	 }
	 else {
		 //alert('has-touch');
		// jQuery("<style>" ).prop("type" , "text/css").html(" .jev-click-to-open {display:none;}").appendTo("head");
	 }
});
