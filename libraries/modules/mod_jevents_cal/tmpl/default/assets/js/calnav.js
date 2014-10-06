function navLoaded(elem, modid) {
	var myspan = document.getElementById("testspan" + modid);
	var modbody = myspan.parentNode;
	modbody.innerHTML = elem.innerHTML;
}
function callNavigation(link) {
	var body = document.getElementsByTagName('body')[0];
	if (!document.getElementById('calnav')) {
		myiframe = document.createElement('iframe');
		myiframe.setAttribute("name", "calnav");
		myiframe.setAttribute("id", "calnav");
		myiframe.style.display = "none";
		body.appendChild(myiframe);
	}
	else {
		myiframe = document.getElementById('calnav');
	}
	myiframe.setAttribute("src", link);
}
