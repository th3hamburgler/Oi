$(document).ready(function() { //perform actions when DOM is ready
	
	// hide the navigation table
	$("#nav_inner").hide();
	
	// bind event to toggle nav
	$("#nav_toggle").click(function () {
		$("#nav_inner").slideToggle("slow");
    });

});