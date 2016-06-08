$(document).ready(function(){
	
	$("#logfile-select").on("change",function(){
		window.location = "/?log="+$(this).val();
		
	});
	
	scale();
	
	$(window).resize(function(){
		scale()
	})
	
});

function scale(){
	var $inner = $("#inner");
	var ww = $(window).width();
	$inner.css({
		"transform": "scale(1)"
	})
	var cw = $inner.outerWidth();
	
	var sc = (ww / cw);
	$inner.css({
		"transform": "scaleX("+sc+")"
	})
	//console.log(ww + " | "+ cw + " | "+sc);
	
	
}