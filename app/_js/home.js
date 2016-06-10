$(document).ready(function(){
	
	$("#logfile-select").on("change",function(){
		window.location = "/?log="+$(this).val();
		
	}).select2();
	$("#scale").on("change",function(){
		scale()	
	});
	
	$(document).on("click",".hour:not(.viewing)",function(){
		var pos = $( "#chart-area" ).scrollLeft();
		$(this).attr("data-left",pos);
		
		var cw = $(this).attr("data-width");
		var $inner = $("#inner");
		var ww = $(window).width();
		var sc = (ww / cw);
		$inner.css({
			"transform": "scaleX("+sc+")"
		});
		
		$(".hour").hide();
		$(this).addClass("viewing").show();
		
	});
	$(document).on("click",".hour.viewing",function(e){
		var $this = $(this);
		var pos = $this.attr("data-left");
		$(".hour.viewing").removeClass("viewing");
		$(".hour").show();
		scale();
		$( "#chart-area" ).scrollLeft( pos );
		$this.removeAttr("data-left")
	});
	
	$(document).on("mousewheel","#chart-area",function(event, delta) {
		
		this.scrollLeft -= (delta * 500);
		
		event.preventDefault();
		
	});
	
	$( "#chart-area" ).scrollLeft( 30000 );
	
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
	});
	$(".hour",$inner).each(function(){
		$(this).attr("data-width",$(this).width());
	})
	var cw = $inner.outerWidth();
	
	var sc = (ww / cw);
	
	if ($("#scale").is(":checked")){
		
	} else {
		sc = 1;
	}
	
	$inner.css({
		"transform": "scaleX("+sc+")"
	})
	//console.log(ww + " | "+ cw + " | "+sc);
	
	
}