$(document).ready( function()
{
	$.listen('dblclick','.codonmodal', function(){ return false; });
	$('#codonmodaldiv').jqm({ajax:'@href',trigger: '.codonmodal'});
	$('#codonmodaldiv').jqmAddTrigger('.codonmodal');
	
	$.listen('click', '.codonlinkajax', function()
	{
		var divupdate = $(this).attr("divupdate");
		var cb = $(this).attr("callback");
		$.ajax({ type: "POST", url: $(this).attr("href"), data: $(this).attr("post"),
			success: 
				function(ret){ 
					$(divupdate).html(ret); 
				
					if($.isFunction(cb)==true){
						eval(cb)(ret);
					} 
				}
		});
		return false;
	});
});