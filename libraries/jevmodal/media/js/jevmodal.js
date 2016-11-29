function jevModalPopup(id, url, title){
	addJevModalHtml(id);
	
	// see http://stackoverflow.com/questions/16152275/how-to-resize-twitter-bootstrap-modal-dynamically-based-on-the-content
	jQuery('#'+id+' .modal-header').css({  'display':'block'});
	jQuery('#'+id+' .modal-title').html(title)
	launchJevModal('#'+id,url);

	return;
}

function jevModalNoHeader(id, url){
	addJevModalHtml(id);

	// see http://stackoverflow.com/questions/16152275/how-to-resize-twitter-bootstrap-modal-dynamically-based-on-the-content
	jQuery('#'+id+' .modal-body').css({  'top':'5px'});
	jQuery('#'+id+' .modal-header').css({  'display':'none'});
	launchJevModal('#'+id,url);
	return;
}

function jevModalNoTitle(id, url){
	addJevModalHtml(id);

	// see http://stackoverflow.com/questions/16152275/how-to-resize-twitter-bootstrap-modal-dynamically-based-on-the-content
	jQuery('#'+id+' .modal-body').css({  'top':'5px'});
	jQuery('#'+id+' .modal-header .close').css({'margin-right': '-15px','margin-top':'-15px','opacity': 1,'font-size:':'30px'});
	jQuery('#'+id+' .modal-header ').css({'height': '0px','z-index':'99','border':'none'});
	jQuery('#'+id+' .modal-header .modal-title').css({  'display':'none'});

	launchJevModal('#'+id,url);
	return;
}

function launchJevModal(selector, url) {
	// Clear the old page!
	jQuery(selector+' iframe').attr("src","about:blank");
	// Will be true if bootstrap 3 is loaded, false if bootstrap 2 or no bootstrap
	var bootstrap3_enabled = (typeof jQuery().emulateTransitionEnd == 'function');
	if (bootstrap3_enabled){
		// This is needed to stop multiple page loads
		jQuery(selector).off('shown.bs.modal');
		jQuery(selector).on('shown.bs.modal', function () {
			//jQuery(selector+' iframe').attr("src","about:blank");
			jQuery(selector+' iframe').attr("src",url);
		});
	}
	else {
		// This is needed to stop multiple page loads
		jQuery(selector).off('shown');
		jQuery(selector).on('shown', function () {
			//jQuery(selector+' iframe').attr("src","about:blank");
			jQuery(selector+' iframe').attr("src",url);
		});
	}
	jQuery(selector).modal({ backdrop: true, show:true, keyboard:true, remote:'' })   // initialized with no keyboard
	return;
}

function addJevModalHtml (id){
	// Will be true if bootstrap 3 is loaded, false if bootstrap 2 or no bootstrap
	var bootstrap3_enabled = (typeof jQuery().emulateTransitionEnd == 'function');
	var myModal="";
	var modalsize='jevmodal-full';
	if (!jQuery("#"+id).length){
		if (bootstrap3_enabled){
			myModal = '<div class="modal   fade ' + modalsize + ' jevmodal" id="'+id+'" tabindex="-1" role="dialog" aria-labelledby="'+id+'Label" aria-hidden="true" >'
				+'<div class="modal-dialog modal-lg">'
					+'<div class="modal-content">'
						+'<div class="modal-header">'
							+'<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
							+'<h4 class="modal-title" id="'+id+'Label"></h4>'
						+'</div>'
						+'<div class="modal-body">'
							+'<iframe src="about:blank;"></iframe>'
						+'</div>'
					+'</div>'
				+'</div>'
			+'</div>';
		}
		else {
			myModal = '<div class="modal  hide fade ' + modalsize + ' jevmodal" id="'+id+'" tabindex="-1" role="dialog" aria-labelledby="'+id+'Label" aria-hidden="true" >'
				+'<div class="modal-dialog modal-lg">'
					+'<div class="modal-content">'
						+'<div class="modal-header">'
							+'<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
							+'<h4 class="modal-title" id="'+id+'Label"></h4>'
						+'</div>'
						+'<div class="modal-body">'
							+'<iframe src="about:blank;"></iframe>'
						+'</div>'
					+'</div>'
				+'</div>'
			+'</div>';
		}
		// see http://stackoverflow.com/questions/10636667/bootstrap-modal-appearing-under-background
		jQuery(myModal).appendTo("body");
	}
}

