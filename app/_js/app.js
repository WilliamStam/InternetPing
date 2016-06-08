$(document).ready(function() {
	
	$(document).on('click', '.btn-row-details', function (e) {
		var $this = $(this), $table = $this.closest("table");
		var $clicked = $(e.target).closest("tr.btn-row-details");
		var active = true;
		
		if ($this.hasClass("active") && $clicked) active = false;
		
		$("tr.btn-row-details.active", $table).removeClass("active");
		if (active) {
			$this.addClass("active");
		}
		
		var show = $("tr.btn-row-details.active", $table).nextAll("tr.row-details");
		
		$("tr.row-details", $table).hide();
		if (show.length) {
			show = show[0];
			$(show).show();
		}
		
	});

	
	
	
});
