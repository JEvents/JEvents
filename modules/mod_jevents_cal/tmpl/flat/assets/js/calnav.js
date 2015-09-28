function callNavigation(link) {
	link += "&json=1";
	//link += "&XDEBUG_SESSION_START=netbeans-xdebug";
	var jSonRequest = jQuery.ajax({
			type : 'GET',
			// use JSONP to allow cross-domain calling of this data!
			dataType : 'jsonp',
			url : link,
			contentType: "application/json; charset=utf-8",
			scriptCharset: "utf-8"
			})
	.done(function(json){
		if (!json || !json.data){
			alert('could not get calendar');
			return;
		}
		if (json.script){
			//alert(json.script);
			eval(json.script);
		}
		var myspan = document.getElementById("testspan" + json.modid);
		var modbody = myspan.parentNode;
		modbody.innerHTML = json.data;
		// we may have tooltips to re-enable too!
		try {
			jQuery(modbody).find('.hasjevtip').popover({'animation':null,'html':true,'placement':'top','selector':null,'title':null,'trigger':'hover focus','content':null,'delay':{'hide':150},'container':modbody});
		}
		catch (e) {
		}

		setupMiniCalTouchInteractions();
	})
	.fail( function( jqxhr, textStatus, error){
		alert(textStatus + ", " + error);
	});
}

// setup touch interaction
jQuery(document).on('ready', function() {
	setupMiniCalTouchInteractions();
});

var jevMiniTouchStartX = false;
var jevMiniTouchStartY = false;
function setupMiniCalTouchInteractions() {
	 if ('ontouchstart' in document.documentElement) {
		 jQuery(".flatcal_weekdays").parent().on("touchend", function (evt) {
			//jevlog("touchend/touchend.");
			//jevlog('changed touches '+ evt.originalEvent.changedTouches.length);
			var touchobj = evt.originalEvent.changedTouches[0]
			 var vdist = touchobj.pageY - jevMiniTouchStartY ;
			 if (Math.abs(vdist)>50) {
				evt.preventDefault();
				return;
			 }
			var distX = touchobj.pageX - jevMiniTouchStartX ;
			if (distX > 10)
			{
				if (linkprevious) callNavigation(linkprevious);
				evt.preventDefault();
			}
			else if (distX < -10)
			{
				if (linknext) callNavigation(linknext);
				evt.preventDefault();
			}
		 });
		 jQuery(".flatcal_weekdays").parent().on("touchstart", function (evt) {
			 //evt.preventDefault();
			 var touchobj = evt.originalEvent.changedTouches[0]
			 jevMiniTouchStartX =  touchobj.pageX;
			 jevMiniTouchStartY =  touchobj.pageY;
		 });
		 jQuery(".flatcal_weekdays").parent().on("touchmove", function (evt) {
			 // top stop scrolling etc. but only in the horizontal
			 var touchobj = evt.originalEvent.changedTouches[0]

			var distX = touchobj.pageX - jevMiniTouchStartX ;
			//jevlog(distX + " "+Math.abs(distX));
			if (Math.abs(distX) > 5)
			{
				evt.preventDefault();
				return;
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
