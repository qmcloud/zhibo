function resize_window(){
	$("#left").height($(window).height()-102);
	$("#right").height($(window).height()-82).width($(window).width()-201);
}
$(function(){
	resize_window();
	$(window).resize(function(){
		resize_window();
	})
})
