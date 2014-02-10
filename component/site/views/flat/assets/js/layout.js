window.addEvent('domready', function(){
	$$(".jev_dayoutofmonth").each(
	function(el){
		if (el.getParent().hasClass("slots1")){
			el.style.height = "81px";
		}
		else {
			var psize = el.getParent().getSize();
			el.style.height=psize.y+"px";
		}
	},this);

});
/*
window.addEvent('load', function(){
	if ($("jevents_body")){
		if($("jevents_body").getStyle('z-index')==2) {
			$$(".jev_daynoevents").each(
			function(el){
				if (el.getParent().hasClass("slots1")){
					el.style.height = "81px";
				}
				else {
					var psize = el.getParent().getSize(;
					el.style.height=psize.y+"px";
				}
			},this);
		}
	}
});*/
