// Repair the stupid toolbar buttons !!!
window.addEvent('domready',
function(){
	var links = $(document.body).getElements('a.toolbar');
	links.each(function(el){
		el.addEvent('click',function(event) {
			event.preventDefault();
		});
	});
}
);

