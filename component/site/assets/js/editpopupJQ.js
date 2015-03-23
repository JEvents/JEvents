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

	//jQuery('#myEditModal .modal-body').css({  'overflow-y':'auto'});
	jQuery('#myEditModal').on('show', function () {
		jQuery('iframe').attr("src","about:blank");
		jQuery('iframe').attr("src",url);
	});

	// see http://stackoverflow.com/questions/10636667/bootstrap-modal-appearing-under-background
	jQuery('#myEditModal').modal({ backdrop: true, show:true, keyboard:true, remote:'' })   // initialized with no keyboard
	//jQuery('#myEditModal').modal({ backdrop: true, show:true, keyboard:true, remote:'' })   // initialized with no keyboard

	//jQuery('#myEditModal').modal('handleUpdate')

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
	// see http://stackoverflow.com/questions/16152275/how-to-resize-twitter-bootstrap-modal-dynamically-based-on-the-content
	jQuery('#myEditModal .modal-body').css({  'top':'5px'});
	jQuery('#myEditModal .modal-header').css({  'display':'none'});
	jQuery('#myEditModal').on('show', function () {
		jQuery('iframe').attr("src","about:blank");
		jQuery('iframe').attr("src",url);
	});
	jQuery('#myEditModal').modal({ backdrop: true, show:true, keyboard:true, remote:'' })   // initialized with no keyboard
	return;
}

function addEditModalHtml (){
	if (!document.getElementById("myEditModal")){
		myEditModal = '<div class="modal  hide fade" id="myEditModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >'
				+'<div class="modal-dialog modal-lg">'
					+'<div class="modal-content">'
						+'<div class="modal-header">'
							+'<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
							+'<h4 class="modal-title" id="exampleModalLabel">'+ Joomla.JText._("JEV_ADD_EVENT") +'</h4>'
						+'</div>'
						+'<div class="modal-body">'
							+'<iframe src="about:blank;"></iframe>'
						+'</div>'
					+'</div>'
				+'</div>'
			+'</div>';
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
	jQuery('#myImportModal').on('show', function () {
		jQuery('iframe').attr("src","about:blank");
		jQuery('iframe').attr("src",url);
	});
	jQuery('#myImportModal').modal({ backdrop: true, show:true, keyboard:true, remote:'' })   // initialized with no keyboard
	return;
}


function addImportPopupHtml (){
	if (!document.getElementById("myImportModal")){
		myImportModal = '<div class="modal  hide fade" id="myImportModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >'
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
		jQuery(myImportModal).appendTo("body");
	}
}
