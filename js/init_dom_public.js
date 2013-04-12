$(document).ready(function() {

	//$("#slidecase_right").slidecase({ animation: { type: "fade" }, titleBar: { autoHide: false }, navigator: { autoHide: true } });

// init vertical slider on each page where exist the #slidecase_events id
	if($("#slidecase_events").length > 0)
		{
			$("#slidecase_events").slidecase({ animation: { type: "swing", interval: 7000 }, titleBar: { enabled: false }, navigator: { autoHide: false } });
		}
	
	// error messages box
	$(".info a,.success a,.warning a,.error a").live('click',function(e){
		$(this).parent().fadeOut("slow");
		e.preventDefault();
		e.stopPropagation();
	   });
	
	getlastactivity();
	setInterval(getlastactivity, 300000);

});

function getlastactivity()
{
	var activity_id = $('#activity_id').val();
	$.ajax({
		url: "index_ajax.php",
		dataType: "json",
		data: { pag: "getlastactivity", activity_id: activity_id },
		success: function(data){
			$('#activity_id').val(data.innerHTML.activity_id);
			$('#activity_board_content').html(data.innerHTML.activity_text);
			$('#activity_board_time').html(data.innerHTML.activity_date);
		}
	});
}