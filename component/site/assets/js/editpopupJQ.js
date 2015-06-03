function jevEditPopup(url){
	// close dialog may not exist for monthly calendar
	try {
		jQuery('.action_dialogJQ').modal('hide');
	}
	catch (e){
	}
	addEditModalHtml();
	// see http://stackoverflow.com/questions/16152275/how-to-resize-twitter-bootstrap-modal-dynamically-based-on-the-content
	jQuery('#myEditModal .modal-header').css({  'display':'block'});
	jQuery('#myEditModal .modal-title').html(Joomla.JText._("JEV_ADD_EVENT"))
	//jQuery('#myEditModal .modal-body').css({  'overflow-y':'auto'});
	launchModal('#myEditModal',url);

	// consider using https://github.com/noelboss/featherlight/#usage instead !
	return;
}

function jevEditTranslation(url, title){
	// close dialog may not exist for monthly calendar
	try {
		jQuery('.action_dialogJQ').modal('hide');
	}
	catch (e){
	}
	addEditModalHtml();
	// see http://stackoverflow.com/questions/16152275/how-to-resize-twitter-bootstrap-modal-dynamically-based-on-the-content
	jQuery('#myEditModal .modal-header').css({  'display':'block'});
	jQuery('#myEditModal .modal-title').html(title)
	//jQuery('#myEditModal .modal-body').css({  'overflow-y':'auto'});
	launchModal('#myEditModal',url);

	// consider using https://github.com/noelboss/featherlight/#usage instead !
	return;
}

function jevEditPopupNoHeader(url){
	// close dialog may not exist for monthly calendar
	try {
		jQuery('.action_dialogJQ').modal('hide');
	}
	catch (e){
	}
	addEditModalHtml();

	// Will be true if bootstrap 3 is loaded, false if bootstrap 2 or no bootstrap
	var bootstrap3_enabled = (typeof jQuery().emulateTransitionEnd == 'function');

	// see http://stackoverflow.com/questions/16152275/how-to-resize-twitter-bootstrap-modal-dynamically-based-on-the-content
	jQuery('#myEditModal .modal-body').css({  'top':'5px'});
	jQuery('#myEditModal .modal-header').css({  'display':'none'});
	launchModal('#myEditModal',url);
	return;
}

function jevEditPopupNoTitle(url){
	// close dialog may not exist for monthly calendar
	try {
		jQuery('.action_dialogJQ').modal('hide');
	}
	catch (e){
	}
	addEditModalHtml();

	// see http://stackoverflow.com/questions/16152275/how-to-resize-twitter-bootstrap-modal-dynamically-based-on-the-content
	jQuery('#myEditModal .modal-body').css({  'top':'5px'});
	jQuery('#myEditModal .modal-header .close').css({'margin-right': '-15px','margin-top':'-15px','opacity': 1,'font-size:':'30px'});
	jQuery('#myEditModal .modal-header ').css({'height': '0px','z-index':'99','border':'none'});
	jQuery('#myEditModal .modal-header .modal-title').css({  'display':'none'});

	launchModal('#myEditModal',url);
	return;
}

function launchModal(selector, url) {
	// Clear the old page!
	jQuery(selector+' iframe').attr("src","about:blank");
	// Will be true if bootstrap 3 is loaded, false if bootstrap 2 or no bootstrap
	var bootstrap3_enabled = (typeof jQuery().emulateTransitionEnd == 'function');
	if (bootstrap3_enabled){
		jQuery(selector).off('shown.bs.modal');
		jQuery(selector).on('shown.bs.modal', function () {
			//jQuery(selector+' iframe').attr("src","about:blank");
			jQuery(selector+' iframe').attr("src",url);
		});
	}
	else {
		jQuery(selector).off('shown');
		jQuery(selector).on('shown', function () {
			//jQuery(selector+' iframe').attr("src","about:blank");
			jQuery(selector+' iframe').attr("src",url);
		});
	}
	jQuery(selector).modal({ backdrop: true, show:true, keyboard:true, remote:'' })   // initialized with no keyboard
	return;
}

function addEditModalHtml (){
	// Will be true if bootstrap 3 is loaded, false if bootstrap 2 or no bootstrap
	var bootstrap3_enabled = (typeof jQuery().emulateTransitionEnd == 'function');
	if (!jQuery("#myEditModal").length){
		if (bootstrap3_enabled){
			myEditModal = '<div class="jevbootstrap"><div class="modal   fade" id="myEditModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >'
				+'<div class="modal-dialog modal-lg">'
					+'<div class="modal-content">'
						+'<div class="modal-header">'
							+'<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
							+'<h4 class="modal-title" id="myModalLabel"></h4>'
						+'</div>'
						+'<div class="modal-body">'
							+'<iframe src="about:blank;"></iframe>'
						+'</div>'
					+'</div>'
				+'</div>'
			+'</div>';
		+'</div>';
	}
		else {
			myEditModal = '<div class="jevbootstrap"><div class="modal  hide fade" id="myEditModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >'
				+'<div class="modal-dialog modal-lg">'
					+'<div class="modal-content">'
						+'<div class="modal-header">'
							+'<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
							+'<h4 class="modal-title" id="myModalLabel"></h4>'
						+'</div>'
						+'<div class="modal-body">'
							+'<iframe src="about:blank;"></iframe>'
						+'</div>'
					+'</div>'
				+'</div>'
			+'</div>';
		+'</div>';
		}
		// see http://stackoverflow.com/questions/10636667/bootstrap-modal-appearing-under-background
		jQuery(myEditModal).appendTo("body");
	}
}

function jevImportPopup(url){
	// close dialog may not exist for monthly calendar
	try {
		jQuery('.action_dialogJQ').modal('hide');
	}
	catch (e){

	}
	addImportPopupHtml();

	// see http://stackoverflow.com/questions/16152275/how-to-resize-twitter-bootstrap-modal-dynamically-based-on-the-content
	//jQuery('#myImportModal .modal-body').css({  'top':'5px'});
	//jQuery('#myImportModal .modal-header').css({  'display':'block'});

	launchModal('#myImportModal',url);

	return;
}


function addImportPopupHtml (){
	// Will be true if bootstrap 3 is loaded, false if bootstrap 2 or no bootstrap
	var bootstrap3_enabled = (typeof jQuery().emulateTransitionEnd == 'function');
	if (!document.getElementById("myImportModal")){
		if (bootstrap3_enabled){
			myImportModal = '<div class="jevbootstrap"><div class="modal  fade" id="myImportModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >'
				+'<div class="modal-dialog modal-sm">'
					+'<div class="modal-content">'
						+'<div class="modal-header">'
							+'<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
							+'<h4 class="modal-title" id="exampleModalLabel">'+ Joomla.JText._("JEV_IMPORT_ICALEVENT") +'</h4>'
						+'</div>'
						+'<div class="modal-body">'
							+'<iframe src="about:blank;"></iframe>'
						+'</div>'
					+'</div>'
				+'</div>'
			+'</div>';
		+'</div>';
		}
		else {
			myImportModal = '<div class="jevbootstrap"><div class="modal  hide fade" id="myImportModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >'
				+'<div class="modal-dialog modal-sm">'
					+'<div class="modal-content">'
						+'<div class="modal-header">'
							+'<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
							+'<h4 class="modal-title" id="exampleModalLabel">'+ Joomla.JText._("JEV_IMPORT_ICALEVENT") +'</h4>'
						+'</div>'
						+'<div class="modal-body">'
							+'<iframe src="about:blank;"></iframe>'
						+'</div>'
					+'</div>'
				+'</div>'
			+'</div>';
		+'</div>';
		}

		jQuery(myImportModal).appendTo("body");
	}
}
