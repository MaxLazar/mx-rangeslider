(function($) {
	Grid.bind("mx_rangeslider", "display", function(cell)
    {
                        var cell_obj = cell.find("input");
                        cell_obj.ionRangeSlider();
    });

    FluidField.on("mx_rangeslider", "add", function(element)
    {
       var element = element.find("input");
        element.ionRangeSlider();
    });
})(jQuery);
