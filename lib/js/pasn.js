$(document).ready( function()
{
	/* ------- GENERAL */
	// Hide message dialog until we need it. 
	$('#message').hide();
	
	// Hide message dialog when someone clicks it.
	$('#message').click( function() {
		$(this).hide();
	});
	
	// Whenever the user scrolls, make sure the message dialog appears at the top of the browser
	$(window).scroll(function()
	{
	  $('#message').animate({top:$(window).scrollTop()+"px" },{queue: false, duration: 350});
	// Whenever the user scrolls, make sure the sidebar dialog appears at the top of the browser
	  $('#sidebar').animate({top:$(window).scrollTop()+"px" },{queue: false, duration: 350});
	});
	
	/* ------- INTERNAL FRIEND MANAGER */
	$('.new_friend_entry').draggable();
	
	$(".bucket_label").droppable({
		accept: ".new_friend_entry",
		//activeClass: 'droppable-active',
		hoverClass: 'bucket_label_hover',
		drop: function(ev, ui) {
			$(ui.draggable).hide();
			var alter_id = $(ui.draggable).attr('alter_id');
			var bucket_id = $(this).attr('bucket_id');
			
			$('#message').html("<img src=\"/HARDCODED/lib/images/ajax-loader.gif\" /><br />Loading. Please wait...");
			$('#message').show();		
				
			$('#message').load('/HARDCODED/action.php?module=AJAXFunctions&page=alterBucketAssignment', {alter_id: alter_id, bucket_id: bucket_id});
			
		}
	});
	
	/* ------- EXTERNAL FRIEND MANAGER */
	$("#extFriendSubmit").click( function (){
		$('#message').show();
	});
	$(".nameGeneratorSubmit").click( function (){
		$('#message').show();
	});
	
	$(".extaltername").click( function() {
		$(this).attr('value','');
	});
	
	/* ------- SURVEY */
	$(".alterBucketBox").accordion({
		alwaysOpen: false,
		selectedClass: "alterBucketBox_activeheader"
	});
	
	// Close the accordion
	$(".alterBucketBox").accordion("activate",-1);
	
	$(".alter").draggable({
		zIndex: 1800
	});
	
	$(".alterselectsubmit").droppable({
		accept: ".alter",
		//activeClass: 'droppable-active',
		hoverClass: 'alterselectsubmit_hover',
		drop: function(ev, ui) {
			$(ui.draggable).hide();
			var alter_id = $(ui.draggable).attr('id');
			var alter_name = $(ui.draggable).attr('name');
			var question_id = $(this).attr('id'); 
									
			$.post('/HARDCODED/action.php?module=AJAXFunctions&page=alterSelectSubmit', {alter_id: alter_id, question_id: question_id});
			$(this).append("<br />" + alter_name);			
		}
	});
	
	$(".networkalterselectsubmit").droppable({
		accept: ".alter",
		//activeClass: 'droppable-active',
		hoverClass: 'networkalterselectsubmit_hover',
		drop: function(ev, ui) {
			$(ui.draggable).hide();
			var alter_id2 = $(ui.draggable).attr('id');
			var alter_name = $(ui.draggable).attr('name');
			var alter_id1 = $(this).attr('id');
									
			$.post('/HARDCODED/action.php?module=AJAXFunctions&page=networkAlterSelectSubmit', {alter_id1: alter_id1, alter_id2: alter_id2});
			$(this).append("<br />" + alter_name);			
		}
	});
	
});
