var JeventsFilters = {
	filters: new Array(),
	reset:function (form){
		JeventsFilters.filters.each(function (item,i) {
			if (item.action){
				eval(item.action);
			}
			else {
				var elem = $(item.id);
				if (!elem && form[item.id]){
					elem = form[item.id];
					elem = $(elem);
				}
				if (elem) {
					try {
						var tag = elem.get('tag');
					}
					catch (e) {
						var tag = elem.getTag();
					}
					if (tag =='select'){
						elem.getElements('option').each(
							function(selitem){
								selitem.selected=(selitem.value==item.value)?true:false;
							}
						);
					}
					else {
						elem.value = item.value;
					}
				}

			}
		});
		if (form.filter_reset){
			form.filter_reset.value = 1;
		}
		form.submit();
	}
}