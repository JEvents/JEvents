function jevEditPopup(url,popupw, popuph){
	// close dialog may not exist for monthly calendar
	try {
		closedialog();
	}
	catch (e){
		
	}
	SqueezeBox.initialize({});
	SqueezeBox.setOptions(SqueezeBox.presets,{'handler': 'iframe','size': {'x': popupw, 'y': popuph},'closeWithOverlay': 0});
	SqueezeBox.url = url;		
	SqueezeBox.setContent('iframe', SqueezeBox.url );
	return;
}

function jevImportPopup(url,popupw, popuph){
	// close dialog may not exist for monthly calendar
	try {
		closedialog();
	}
	catch (e){
		
	}
	SqueezeBox.initialize({});
	SqueezeBox.setOptions(SqueezeBox.presets,{'handler': 'iframe','size': {'x': popupw, 'y': popuph},'closeWithOverlay': 0});
	SqueezeBox.url = url;		
	SqueezeBox.setContent('iframe', SqueezeBox.url );
	return;
}

