(function($) {
	Grid.bind("mx_rangeslider", "display", function(cell)
    {
                        var cell_obj = cell.find("input");
                        cell_obj.ionRangeSlider();
    });
})(jQuery);
