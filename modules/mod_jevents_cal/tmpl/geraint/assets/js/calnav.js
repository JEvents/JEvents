
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
		var myspan = document.getElementById("testspan" + json.modid);
		var modbody = myspan.parentNode;
		modbody.innerHTML = json.data;

	})
	.fail( function( jqxhr, textStatus, error){
		alert(textStatus + ", " + error);
	});
}
