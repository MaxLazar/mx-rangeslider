(function($) {
	var onDisplay = function(cell){

		var $select = $('input', cell.dom.$td),
			cell_id =  cell.field.id;
			cell_id =  cell_id.replace(/\[/g, "_");
			cell_id =  cell_id.replace(/\]/g, "_");

			id = cell_id +'_'+cell.row.id+'_'+cell.col.id+'_'+Math.floor(Math.random()*100000000);

			$select.attr('id', id);
			$("#" +  id).ionRangeSlider();
	//		$('div.publish_field.publish_matrix').css({'overflow' : 'visible'});


	};

	Matrix.bind('mx_rangeslider', 'display', onDisplay);

})(jQuery);
