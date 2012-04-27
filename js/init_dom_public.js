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

});