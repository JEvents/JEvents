var JeventsFilters = {
	filters: new Array(),
	reset:function (form){
		JeventsFilters.filters.each(function (item,i) {
			if (item.action){
				eval(item.action);
			}
			else {
				var elem = $(item.id);
				if (elem) elem.value = item.value;

			}
		});
		form.submit();
	}
}