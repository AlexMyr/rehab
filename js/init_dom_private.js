/*
dump = function(string)
	{
		console.log(string);
	}
*/
var delValue = Array();
doExerciseErr = function(textString)
	{
		var error = $('<span/>')
			.attr('id','error')
			.css('padding', '5px 10px')
			.css('display', 'inline-block');
		if($("#exercise_id").attr('value')=="") 
			{
				if($("#error").length === 0)
					{
						if($("#program_list").length > 0)
						{
							error.prependTo($('#program_list'));
							$('#error').text(textString);
							$('#error').fadeOut(5000,function(){$('#error').remove()});
						}
					}
				else 
					{
						$('#error').text(textString); 			
							$('#error').fadeOut(5000,function(){$('#error').remove()});
					}
				return false
			}
		$('#error').fadeOut(5000,function(){$('#error').remove()});
	}
doSave = function()
	{
		if($("#sortable").length > 0)
			{
				var exercises="";
				$("#sortable li").each(function(i, item) 
					{
						if($(this).attr('id')!="") exercises += parseInt($(this).attr('id'))+",";
					});
				$("#exercise_id").attr('value',exercises);
			}
		if($("#exercise_id").attr('value')=="") 
			{
				doExerciseErr('you need to add exercises before trying to save!');
				return false;
			}
		else $("form#exerciseAddForm")[0].submit();
	}
makeSortable = function()
	{
		if($("#sortable").length > 0)
			{
				$( "#sortable" ).sortable({
					revert: true,
					axis: 'y',
					containment: '.sidebar',
					start: function(event, ui)
							{
								$('span .exercise_text').removeClass('displayBlock');
							}
				});
			}		
	}
makeDelete = function()
	{
		if($("#sortable").length > 0)
			{
				$( "li a.exercise_del" ).each(function(i, item) 
					{
						if($(this).attr('id')!="")
							{
								$(this).bind('click',function(e)
									{ 
										e.preventDefault(); 
										e.stopPropagation(); 
										$(this).unbind("click");
										id = parseInt($(this).attr('id').split('del_')[1]);
								delValue[id] = id;
										//check section program or client
										if($(this).hasClass('program_del'))
											rmPExercise(id);
										else
											rmExercise(id);
									}); 
							}	
					});
			}		
	}
rmExercise = function(rm_pid)
	{
		$.post("index_ajax.php", { pag: "xgetexercise", rm_pid: rm_pid }, function(data){ 
		var obj = $.evalJSON(data);
		if(obj.failure==true) 
			{
				doExerciseErr('an error ocured. No data to show!');
			}	
		else 
			{
				// if error block exist - remove it first
				if($("#program_list #error").length > 0)
					{
						$('#error').fadeOut(5000,function(){$('#error').remove()});
					}
				if(obj.innerHTML.err=='404')
					{
						doExerciseErr('an error ocured while removing the exercise');
					}
				else if(obj.innerHTML.err=='200')
					{
						delValue.splice(rm_pid, 1);
						$("#program_list li#"+rm_pid).remove();
						$("#program_list span#text_"+rm_pid).remove();
						doExerciseErr('exercise removed');
					}
				}
			 });
	}
	
rmPExercise = function(rm_pid)
	{
		$.post("index_ajax.php", { pag: "pgetexercise", rm_pid: rm_pid }, function(data){ 
		var obj = $.evalJSON(data);
		if(obj.failure==true) 
			{
				doExerciseErr('an error ocured. No data to show!');
			}	
		else 
			{
				// if error block exist - remove it first
				if($("#program_list #error").length > 0)
					{
						$('#error').fadeOut(5000,function(){$('#error').remove()});
					}
				if(obj.innerHTML.err=='404')
					{
						doExerciseErr('an error ocured while removing the exercise');
					}
				else if(obj.innerHTML.err=='200')
					{
						delValue.splice(rm_pid, 1);
						$("#program_list li#"+rm_pid).remove();
						$("#program_list span#text_"+rm_pid).remove();
						doExerciseErr('exercise removed');
					}
				}
			 });
	}

doExerciseDetails = function()
	{
		if($("#program_list li .exercise_details").length > 0)
		{
			$('#program_list li .exercise_details').each(function(i, item)
			{
				var details = parseInt($(this).attr('id').split('details_')[1]);
				if($(this).parent().find('#text_'+details).length > 0)
				{
					var obj = $('#text_'+details);
					obj.remove();
					obj.appendTo($(this).parent().parent().parent());
					$(this).bind('click',function(e)
					{
						//var scrolledSize = $('#content').scrollTop();
						var scrolledSize = (-1)*parseInt($('.jspPane').css('top'));

						var details = parseInt($(this).attr('id').split('details_')[1]);
						e.stopPropagation();
						e.preventDefault();
						var body = $(document).width();
						var content = $('.siteBody').width();
						var margins = body-content;
						var size = $(this).offset();
						var thetop = size.top-79 + scrolledSize;
						var theleft = size.left-220;
						obj.css({
							'top':thetop,
							'right':'11px'
						});
					
						if(!$('#text_'+details).hasClass('displayBlock'))
							$('.exercise_text').removeClass('displayBlock');
						$('#text_'+details).toggleClass('displayBlock');
						if($(this).hasClass('edit')){
							editDescription($(this));
						}
					});	
				}
			});
		}
	}

doExerciseCompactViewDetails = function()
	{
		if($(".itemCompact .programCompact").length > 0)
		{
			$('.itemCompact .programCompact .exercise_details').each(function(i, item)
			{
				var details = parseInt($(this).attr('id').split('compactViewDetails_')[1]);
				if($(this).parent().parent().find('#itemCompactText_'+details).length > 0)
				{
					var obj = $('#itemCompactText_'+details);
					obj.remove();
//							obj.appendTo($(this).parent().parent().parent());
					obj.appendTo($(this).parent().parent().parent());
					$(this).bind('click',function(e)
					{
						//var scrolledSize = $('#content').scrollTop();
						var scrolledSize = (-1)*parseInt($('.jspPane').css('top'));
						
						var details = parseInt($(this).attr('id').split('compactViewDetails_')[1]);
						e.stopPropagation();
						e.preventDefault();
						var body = $(document).width();
						var content = $('.siteBody').width();
						var margins = body-content;
						var size = $(this).offset();
						//sub some constant value to postion
						var thetop = size.top-80 + scrolledSize;
						var theleft = size.left-(margins/2)-125;
						obj.css({
							'top':thetop,
							'left':theleft
						});
						if(!$('#itemCompactText_'+details).hasClass('displayBlock'))
							$('.programCompactText').removeClass('displayBlock');
						$('#itemCompactText_'+details).toggleClass('displayBlock');
					});	
				}
			});
		}
	}

doExercise = function(jSON)
	{
		// eval json data
		var obj = $.evalJSON(jSON);
		$('.moreBtn').each(function(){
		if($(this).attr('id') == obj.pid || $(this).attr('id') == obj.innerHTML.PROGRAM_ID)
			{
				
				$(this).html('<span>Added</span>');
			}
		})
//		var obj = $.parseJSON(jSON);
//		 dump(jSON); // WARNING, this function craches the JS in Internet Exploder, use it carefully
		
		// check if container exist, if not create it
		if($("#sortable").length === 0)
			{
				if($("#program_list").length > 0)
					{
						var exBlockUL = $('<ul/>')
							.attr('id', 'sortable')
							.attr('class', 'ui-sortable')
							.css('position', 'relative');
						exBlockUL.appendTo($("#program_list"));
						$("#program_list")
							.css('position', 'relative');
					}
			}
		if(obj.failure==true) 
			{
				doErrMess('an error ocured. No data to show!');
			}	
		else 
			{
				// if error block exist - remove it first
				if($("#program_list #error").length > 0)
					{
						$('#error').fadeOut(5000,function(){$('#error').remove()});
					}
				if(obj.innerHTML.err=='404')
					{
						doExerciseErr('exercise already exist');
					}
				else if(obj.innerHTML.err=='200')
					{
						/* HERE WE GO AGAIN WITH A LONG PART OF CREATING NEW HTML ELEMENTS FOR THE SORTABLE */
				
						/* THIS ARE THE JSON RETURNED DATA FOR SORTABLE */
						
						/*
							1.	PROGRAM_ID
							2.	PROGRAM_TITLE
							3.	PROGRAM_DESCRIPTION
							4.	PROGRAM_IMAGE
						*/
						// define all blocks and data
						var exLI_img = $('<div>').addClass(obj.innerHTML.PROGRAM_POS)
							.css({
								"background-image": "url('../upload/thumbs/"+obj.innerHTML.PROGRAM_IMAGE+"')",
								"width": "64px",
								"height": "64px",
								"float": "left",
								"margin-right": "5px"
							});
						
						var thumbpath = "phpthumb/phpThumb.php?src=../";
//						var thumbsize = "&amp;wl=50&amp;hp=50"; // this crashes
						var thumbsize = "&wl=64&hp=64";
						var exUL = $("#sortable");
						var exLI = $('<li/>')
							.attr('id', obj.innerHTML.PROGRAM_ID);
						//var exLI_img = $('<img/>')
						//	.attr('alt', obj.innerHTML.PROGRAM_TITLE)
						//	.attr('title', obj.innerHTML.PROGRAM_TITLE)
						//	.attr('width', 64)
						//	.attr('height', 64)
						//	.attr('src',thumbpath + obj.innerHTML.PROGRAM_IMAGE + thumbsize);
//							.attr('src',obj.innerHTML.PROGRAM_IMAGE);
						var exLI_title = $('<span/>')
							.attr('id', 'title_'+obj.innerHTML.PROGRAM_ID)
							.attr('class','exercise_title');
						var exLI_titleNode = $(document.createTextNode(obj.innerHTML.PROGRAM_TITLE));
						var exLI_text = $('<span/>')
							.attr('id', 'text_'+obj.innerHTML.PROGRAM_ID)
							.attr('class','exercise_text');
						
						var exLI_textNode = $(document.createTextNode(obj.innerHTML.PROGRAM_DESCRIPTION));
						
						var exLI_cat = $('<span/>')
							.attr('class','exercise_cat');
//						var exLI_catNode = $(document.createTextNode(obj.innerHTML.PROGRAM_CATEGORY));
						var exLI_catNode = obj.innerHTML.PROGRAM_CATEGORY;

						if(window.location.href.indexOf('program_update_exercise')>0)
							var exLI_delete = $('<a/>')
								.attr('id', 'del_'+obj.innerHTML.PROGRAM_ID)
								.attr('class','exercise_del program_del')
								.attr('href','#');
						else
							var exLI_delete = $('<a/>')
								.attr('id', 'del_'+obj.innerHTML.PROGRAM_ID)
								.attr('class','exercise_del')
								.attr('href','#');
						
						if(window.location.href.indexOf('program_update_exercise')>0){
							var exLI_details = $('<a/>')
								.attr('id', 'details_'+obj.innerHTML.PROGRAM_ID)
								.attr('class','exercise_details edit');
						}
						else{var exLI_details = $('<a/>')
								.attr('id', 'details_'+obj.innerHTML.PROGRAM_ID)
								.attr('class','exercise_details');
						}
						var exLI_drag = $('<a/>')
							.attr('id', 'drag_'+obj.innerHTML.PROGRAM_ID)
							.attr('class','exercise_drag')
							.attr('href','#');
						var exDIV_clear = $('<div/>')
							.attr('class','clearAll');
					
						// append all childs
						exLI_img.appendTo(exLI);
						exLI_delete.appendTo(exLI);
						exLI_delete.html('&nbsp;');
						exLI_drag.appendTo(exLI);
						exLI_drag.html('&nbsp;');
						exLI_title.appendTo(exLI);
						exLI_titleNode.appendTo(exLI_title);
						exLI_cat.appendTo(exLI);
//						exLI_catNode.appendTo(exLI_cat);
						exLI_cat.html(exLI_catNode);
						exLI_details.appendTo(exLI);
						if(window.location.href.indexOf('program_update_exercise')>0)
							exLI_details.html('Edit info');
						else
							exLI_details.html('details');
							
						if(window.location.href.indexOf('program_update_exercise')>0){
							var div = $('<div/>');
							exLI_text.appendTo(exLI);
							div.appendTo(exLI_text);
							exLI_textNode.appendTo(div);
						}
						else{
							exLI_text.appendTo(exLI);
							exLI_textNode.appendTo(exLI_text);
						}
						exDIV_clear.appendTo(exLI);
						exLI.appendTo(exUL);

						exLI_delete.bind('click',function(e)
							{ 
								e.preventDefault(); 
								e.stopPropagation(); 
								delValue[obj.innerHTML.PROGRAM_ID] = obj.innerHTML.PROGRAM_ID;
								if(window.location.href.indexOf('program_update_exercise')>0)
									rmPExercise(obj.innerHTML.PROGRAM_ID); 
								else
									rmExercise(obj.innerHTML.PROGRAM_ID); 
							});
						
					}
			}
		
		makeSortable();
		//makeDelete();
		doExerciseDetails();
	}

function restoreDefaultPageReload()
{
	window.pageReload = false;
}

function restoreDefaultBodyClicked()
{
	window.bodyClicked = false;
}

editDescription = function(obj){
		var id = $(obj).attr('id').split('_').pop();
		var descr = $('#text_'+id);
		if($(descr).has($(descr).children('textarea')).length == 0){
			descr.children('div').hide(0);
			descr.prepend('<textarea name="custom_descr['+id+']">'+descr.children('div').html()+'</textarea>'+
				'<span class="save">Save</span><span class="cancel">Cancel</span>');
		}
}

function AddToFavorites(obj, title, url){
   	var ua = navigator.userAgent.toLowerCase();
	var isWebkit = (ua.indexOf('webkit') != - 1);
	var isMac = (ua.indexOf('mac') != - 1);

  	if(isWebkit || isMac)
  	{
  		alert('If you wand add this site to favirotes - click CTRL+D');
  		return false;
  	}
  	else if(window.sidebar){
    	// Mozilla Firefox Bookmark
    	window.sidebar.addPanel(title, url,"");
    	return false;
  	} else if(window.external){
    	// IE Favorite
    	window.external.AddFavorite( url, title);
    	return false;
  	} else if(window.opera && window.print){
    	//Opera Hotlist
    	obj.setAttribute('href',url);
    	obj.setAttribute('title',title);
    	obj.setAttribute('rel','sidebar');
    	obj.click();
    	return false;
  	}
}

/**
 * disallow data copying using Ctrl+C
 */
function onKeyDown(event)
{
    var pressedKey = String.fromCharCode(event.keyCode).toLowerCase();

    if (event.ctrlKey && (pressedKey == "c"))
        event.returnValue = false;
}
/**
 * disallow data copying using HTML attributes
 * oncopy="return false;" oncut="return false;" ondragstart="return false;"
 * 
 * @TODO: change it to events listening through bind method
 * @TODO remove all these attributes from HTML files
 */
$(function(){
    $('body')
        .attr('oncopy', 'return false;')
        .attr('oncut', 'return false;')
        .attr('ondragstart', 'return false;');
})

// START THE DOCUMENT READY
$(document).ready(function() 
{
	document.oncontextmenu = function(){ return false;}
	
	//get list of subcats for add exercise page
	$('#categorySelect').change(function(){
		var catid = $(this).val();
		
		if(catid != -1)
		{
			$.ajax({
				url: "index_ajax.php",
				dataType: "json",
				data: { pag: "getsubcats", catid: catid},
				success: function(data){
					$('#subcategorySelect').html(data.innerHTML);
				}
			});
		}
		
	});
	
	$('.navMenu .item1').click(function(e){
		if(navigator.userAgent.match(/iPad/i) != null)
		{
			var el = $(this);
			if(el.attr('href') == 'index.php?pag=profile' || el.attr('href') == 'index.php?pag=programs')
			{
				e.preventDefault();
			}
		}
	});

	
	$('#clientsList').dialog({  autoOpen: false, resizable: false })
	$('#pdfInfo').dialog({  autoOpen: false, resizable: false, title: "Info" })
	
	$('.clientList .clientListRow .actions a.program, #add_patient_button').click(function(){
		
		if($('#clientsList div').html() == '')
		{
			$('#clientsList').dialog( "option" , "title", $(this).attr('ptitle') );
			
			var pid = $(this).attr('pid');
		
			$.ajax({
				url: "index_ajax.php",
				dataType: "json",
				data: { pag: "getclients", pid: pid},
				success: function(data){
					$('#clientsList').css('height', '300px');
					
					$('#clientsList div').html(data.innerHTML);
					
					if(typeof($('#clientsList').jScrollPane()) != 'undefined')
						$('#clientsList').jScrollPane();
					
					$('#clientsList').css('width', '300px');
					$('.jspContainer').css('width', '300px');
				}
			});
		}
		
		$('#clientsList').dialog('open');
	});
	
	$('#pdfInfoImg').click(function(){
		$('#pdfInfo').dialog('open');
	});
	
	
	$( "#clientsList div" ).bind( "dialogclose", function(event, ui) {
		$('#clientsList div').html('');
	});
	
	//add scroll
	if(typeof($('.clientListDynamic').jScrollPane()) != 'undefined')
		$('.clientListDynamic').jScrollPane();
		
	if(typeof($('.jsScrollDiv').jScrollPane()) != 'undefined')
		$('.jsScrollDiv').jScrollPane();
	
	
	if(typeof($('.videoList').jScrollPane()) != 'undefined')
		$('.videoList').jScrollPane();
		
	if(typeof($('.programListDynamic').jScrollPane()) != 'undefined')
		$('.programListDynamic').jScrollPane();

	$('#filterPatientsUrl').click(function(e){
		e.preventDefault();
		window.location = $(this).attr('href') + '&query=' + $('#filterPatientsValue').val();
	});
	$("#filterPatientsValue").keypress(function(e) {
		if ( e.which == 13 ) {
			window.location = $('#filterPatientsUrl').attr('href') + '&query=' + $(this).val();
		}
	});
	
	$('#searchExerciseButton').click(function(e){
		e.preventDefault();
		window.location = $(this).attr('href') + '&query=' + $('#searchExerciseInput').val();
	});
	$("#searchExerciseInput").keypress(function(e) {
		if ( e.which == 13 ) {
			window.location = $('#searchExerciseButton').attr('href') + '&query=' + $(this).val();
		}
	});
	
	$('.changeVideoUrl').click(function(){
		//var newUrl = "http://www.youtube.com/v/"+$(this).attr('url')+"?version=3&f=videos&app=youtube_gdata&autoplay=0";
		var newUrl = "http://www.youtube.com/embed/"+$(this).attr('url');
		$('#currentVideo').attr('src', newUrl);
	})
	
	//add corner
	if(jQuery.browser.msie == 'undefined' || (jQuery.browser.msie && jQuery.browser.version>8))
	{
		if($('.breadcrumb').length > 0)
			$('.breadcrumb').corner('5px');
		if($('.compact').length > 0)
			$('.compact').corner('right 5px');
		if($('.curvyCorner').length > 0)
			$('.curvyCorner').corner('5px');
		//if($('input, textarea, select').length > 0)
		//	$('input, textarea, select').corner('5px');
		if($('.deleteClient button.del b').length > 0)
			$('.deleteClient button.del b').corner('left 5px');
		if($('.deleteClient button.del span').length > 0)
			$('.deleteClient button.del span').corner('right 5px');
	}
	
	
	//posX = null;
	//posY = null;
	redirect_url = null;
	
	window.bodyClicked = false;
	window.pageReload = false;
	
	$.post("index_ajax.php", { test: "test"}, function(data){
		if(data == 'error' || parseInt(data) == NaN)
		{
			jQuery(".changeURL a").each(function(){
				jQuery(this).attr('href', 'index.php?pag=profile');
			});
		}
	});
	
	$('body').click(function(){
		window.bodyClicked = true;
		setTimeout('restoreDefaultBodyClicked', 500);
	});
	
	$('body').keypress(function(event) {
		if ( event.keyCode == 116 ) {
			window.pageReload = true;
			setTimeout('restoreDefaultPageReload', 500);
		}
	});

	//$(document).bind('mousemove', function(e){
	//	if (typeof e == 'undefined')
	//		myEvent = window.event;
	//	else
	//		myEvent = e;
	//	posX = myEvent.clientX;
	//	posY = myEvent.clientY;
	//});

 	$(window).bind('beforeunload', function(event) {
		if(jQuery('input[name="act"]').val()=='client-update_exercise_plan')
		{
			//if(posY == null || posY <= 25)
			if(!window.bodyClicked)
			{
				redirect_url = jQuery('.preview_buttons .moreBtn:first').attr('href');
			}
			else
				redirect_url = null;
		}
		else 	if(jQuery('input[name="act"]').val()=='client-update_exercise')
		{
			//if(posY == null || posY <= 25)
			if(!window.bodyClicked)
			{
				redirect_url = 'index.php?pag=dashboard';
			}
			else
				redirect_url = null;
		}
	});
		
	$(window).bind('unload', function() {
		if(window.pageReload)
		{
			window.location.reload;
		}
 	 	else if(jQuery('input[name="act"]').val()=='client-update_exercise_plan' && redirect_url != null)
		{
			var redirect = redirect_url;
			redirect_url = null;
			window.location = redirect;
		}
		else if(jQuery('input[name="act"]').val()=='client-update_exercise' && redirect_url != null)
		{
			var redirect = redirect_url;
			redirect_url = null;
			window.location = redirect;
		}
	});
	
	//add submenu
	function show_submenu(obj, submenu)
	{
		hovered = true;
		obj.addClass('topForSubMenu');
		var parOffset = obj.offset();
		var parHeight = obj.css('height');
		submenu.css('top', parOffset.top+parseInt(parHeight));
		submenu.css('left', parOffset.left+'px');
		submenu.css('display', 'block');
		if(submenu.data('hideTimeout'))
			window.clearTimeout(submenu.data('hideTimeout'));
	}
	
	var hovered = false;
	var current = false;
	var parent = false;
	

	function hide_submenu(selector)
	{
      if(hovered)
      {
		$('.item1').removeClass('topForSubMenu');
		$(selector).data('hideTimeout', setTimeout(function(){$(selector).hide(0);}, 1));
      }
	}
	$('.navMenu .item1').hover(
		function()
		{
			var el = $(this);
			if(el.attr('href') == 'index.php?pag=profile')
			{
				parent = el;
				current = $('#submenuList');
				show_submenu(el, current);
			}
			else if(el.attr('href') == 'index.php?pag=programs')
			{
				parent = el;
				current = $('#submenuProgramList');
				show_submenu(el, current);
			}
		},
		function()
		{
			if(hovered)
			{
				hide_submenu(current);
			}
		});
		
	$('#submenuList').hover(
		function(){
			hovered = true;
			parent.addClass('topForSubMenu');
			if($(this).data('hideTimeout'))
				window.clearTimeout($(this).data('hideTimeout'));
		},
		function(){
			$('.item1').removeClass('topForSubMenu');
			hide_submenu('#submenuList');
			hovered = false;
		}
	);	
	
	$('#submenuProgramList').hover(
		function(){
			hovered = true;
			parent.addClass('topForSubMenu');
			if($(this).data('hideTimeout'))
				window.clearTimeout($(this).data('hideTimeout'));
		},
		function(){
			$('.item1').removeClass('topForSubMenu');
			hovered = true;
			hide_submenu("#submenuProgramList");
		}
	);
		
	//save minimized li to cookies
	$('.program_menu li span').click(function(){
		var cokieId = $(this).parent().attr('id');
		var cookieVal = $(this).parent().hasClass('on');
		var cookieOption = {
			path: '/',
			expiresAt: new Date( 2020, 1, 1 )
		};
	
		if(cookieVal)
		{
			$.cookies.set(cokieId , 'on', cookieOption);
		}else
		{
			$.cookies.set(cokieId , 'off', cookieOption);
		}
	});
	
	$('.breadcrumb span.buttons a').click(function(){
		var cookieName = 'currentExerciseViewType';
		var hasNeededClass = $(this).hasClass('details');
		var cookieVal = 'compact';
		if(hasNeededClass)
			cookieVal = 'details';
		var cookieOption = {
			path: '/',
			expiresAt: new Date( 2020, 1, 1 )
		};
		$.cookies.set(cookieName , cookieVal, cookieOption);
	});
	
	$('.item .light_box, .itemCompact .light_box').click(function(){
		var img = $(this).attr('imgname');
		if(!img || img.indexOf('noimage')>-1 || img.indexOf('pdf')>-1)
			return false;
		
		var lightBox = $('<div id="innerLightBoxDiv"><img src="phpthumb/phpThumb.php?src=../upload/'+img+'&wl=300&hp=300" /></div>');
		$('#imgLightBox').css('left', '500px');
		$('#imgLightBox').css('top', '200px');
		$('#imgLightBox').css('z-index', '9999999');
		$('#imgLightBox').html(lightBox);
		$('#imgLightBox').show();
	});
	
	$('#innerLightBoxDiv').live('click', function(){
		$(this).parent().hide();
	});
	
	$("#scrollToTop").click(function(){
		$(".jspPane").css('top', 0);
		$(".jspDrag").css('top', 0);
		$(".scrolledList").scrollTop(0);
		$(window).scrollTop(0);
	});
	
	$('#submitPayment').click(function(e){
		e.preventDefault();
		if($(this).data('planChosen')!=true)
		{
			var alertValue = $('span.expire_time span').text();
			alert(alertValue);
			return false;
		}
		else
			window.location = $(this).attr('href');
	});
	
	//$('.showLimitError').click(function(e){
	//	e.preventDefault();
	//	if(confirm("Trial user can't add more than 5 programs. Do you want to upgrade your plan?"))
	//	{
	//		window.location = 'index.php?pag=profile_payment';
	//	}
	//	else
	//	{
	//		return false;
	//	}
	//});
	
		// error messages box
	$(".info a,.success a,.warning a,.error a").live('click',function(e)
	{
		$(this).parent().fadeOut("slow");
		e.preventDefault();
		e.stopPropagation();
	});

	// make the category / subcategory list menu for the ADD CLIENT EXERCISE PAGE
	if($(".programCategoryList").length > 0)
	{
		$('.programCategoryList ul li.parent span').live('click',function(e){
			$(this).parent().find('ul').toggle("slow");
			$(this).parent().toggleClass("on");
		//	e.preventDefault();
			e.stopPropagation();
		});
		if($('.programCategoryList ul li.parent ul').length > 0)
			$('.programCategoryList ul li.parent ul').parent().toggleClass("on");
	}

	// PREPARE to add program to client
	if($(".programText").length > 0)
	{
	   $('.moreBtn').live('click',function(e)
		{
			if($(this).hasClass('programBtn'))
			{
				var pid = $(this).attr('id');
				var epid = $(this).attr('epid');
				e.stopPropagation();	
				e.preventDefault();
				$.post("index_ajax.php", { pag: "pgetexercise", pid: pid, epid: epid }, function(data){ doExercise(data); });
			}
			else
			{
				var pid = $(this).attr('id');
				var cid = $(this).attr('cid');
				e.stopPropagation();	
				e.preventDefault();
				$.post("index_ajax.php", { pag: "xgetexercise", pid: pid, cid: cid }, function(data){ doExercise(data); });
			}
				//	$.getJSON('index_ajax.php?pag=xgetexercise&pid='+pid, function(data) { doExercise(data); });
		});	
	}
	
	if($(".programCompact").length > 0)
	{
	   $('.moreBtn').live('click',function(e)
		{
			if($(this).hasClass('programBtn'))
			{
				var pid = $(this).attr('id');
				var epid = $(this).attr('epid');
				e.stopPropagation();	
				e.preventDefault();
				$.post("index_ajax.php", { pag: "pgetexercise", pid: pid, epid: epid }, function(data){ doExercise(data); });
			}
			else
			{
				var pid = $(this).attr('id');
				var cid = $(this).attr('cid');
				e.stopPropagation();	
				e.preventDefault();
				$.post("index_ajax.php", { pag: "xgetexercise", pid: pid, cid: cid }, function(data){ doExercise(data); });
			}
			//	$.getJSON('index_ajax.php?pag=xgetexercise&pid='+pid, function(data) { doExercise(data); });
		});	
	}
	
	$("#exerciseAdd").bind('click',function(e)
	{
		window.bodyClicked = true;
		e.stopPropagation();	
		e.preventDefault();
		doSave();
	});
	makeSortable();
	makeDelete();
	doExerciseDetails();
	doExerciseCompactViewDetails();
	
	$('.lang a').click(function(){
		var cookieName = 'language';
		var cookieVal = $(this).attr('value');
		var cookieOption = {
			path: '/',
			expiresAt: new Date( 2020, 1, 1 )
		};
		$.cookies.set(cookieName , cookieVal, cookieOption);
	});
	
	//delete image preview
	$('img.delete_image').click(function(e){
		$.ajax({url: "index_ajax.php",
				dataType: "json",
				data: { programs_id: $("input.[name='programs_id']").val(), act: 'member-delete_image' }
				})
			.done(function( msg ) {
				if(msg.failure == false){
					$('img.delete_image, .image_preview').hide(200);
					$('#image_uploaded').val('0');
				}
			}
		);
	});
	
	// make current url for different languages at top flags
	var regExpr = /\/..\/.*/i;					
	if(regExpr.exec(window.location.pathname)){
		$('#lang_en').attr('href', (window.location.pathname+window.location.search).toString().substr(3));
		$('#lang_us').attr('href', (window.location.pathname+window.location.search).toString());
	}else{
		$('#lang_en').attr('href', (window.location.pathname+window.location.search).toString());
		$('#lang_us').attr('href', ('/us'+window.location.pathname+window.location.search).toString());
	}
	
	$('#modify_program_button').click(function(e){
		if($(this).attr('href') == 'javascript:void(0);')
			return false;
		
		e.preventDefault(); 
		var url = $(this).attr('href');
		var first = escape($('#first_name').val());
		var surname = escape($('#surname').val());
		var appeal = escape($('#appeal').val());
		var email = escape($('#email').val());
		url += '&first='+first+'&surname='+surname+'&appeal='+appeal+'&email='+email;
		location.href = url;
	});
	
	$('#addExerciseToClient').click(function(){
		//$('#exercise_desc').val($('#client_note').val()); //no more need as user can enter plan descr at exercises adding page
		$('#create_exercise').submit();
		return true;
	});
	
	$('#advanced').click(function(){ 
		$('#advanced_popup').show(100, function(){
			$(document).click(function(e){
				if( $('#advanced_popup').children().index($(e.target)) == -1 && 
						$(e.target).attr('id') != 'advanced_popup'){
					$('#advanced_popup').hide(100);
					$(document).unbind('click');
				}
			});
		});  
	});
	
	$('#reset_size').click(function(){ 	$('input[name="width"]').val(300); 
										$('input[name="height"]').val(100);
	});
	
	$('#resize_image').click(function(){
		var width = $('input[name="width"]').val(); 
		var height =  $('input[name="height"]').val();
		
		$.ajax({
            url: "index_ajax.php",
            dataType: "json",
            data: { act: "member-resize_existing_image",
                    width: width,
					height: height
            },
            success: function(data){
				var cur_src = $('#header_image').attr('src');
				cur_src += Math.round(Math.random()*10);
				$('#header_image').attr('src', cur_src);
            }
        });
	});
	
	
	$('#exercise_desc, #program_desc').click(function(){
		if( $(this).val() == $(this).attr('jsplaceholder') ){
			$(this).val('');
		}
	});
	$('#exercise_desc, #program_desc').blur(function(){
		if( $(this).val() == '' ){
			$(this).val( $(this).attr('jsplaceholder') );
		}
	});
    
    $('span.save').live('click', function(e){
        var id = $(this).parent('span.exercise_text').attr('id').split('_').pop();
        var descr = $('#text_'+id);
        $(descr).removeClass('displayBlock');
        descr.children('div').html( descr.children('textarea').val() );
        $.ajax({
            url: "index_ajax.php",
            dataType: "json",
            data: { act: "client-update_custom_description",
                    ex_id: id,
                    program_id: $('input[name=program_id]').val(),
                    descr: descr.children('textarea').val() },
            success: function(data){
            }
        });
    });
    $('span.cancel').live('click', function(e){
        var id = $(this).parent('span.exercise_text').attr('id').split('_').pop();
        var descr = $('#text_'+id);
        descr.removeClass('displayBlock');
        descr.children('div').show();
        descr.children('textarea, span').remove();
    });
   
    $('input[type=file]').change(function (){
        $('.input input[type=text]', $(this).parent('.browse_field')).val($(this).val());
    });
	
	$('#removeHeaderImage').click(function(){
		$(this).parent().html('');
		$.ajax({
            url: "index_ajax.php",
            dataType: "json",
            data: { act: "member-erasecustompicture" }
		});
	});
	
	$('.favImg').click(function(e){
		e.stopPropagation();
		if($(this).hasClass('starGold'))
		{
			$(this).removeClass('starGold');
			$(this).addClass('starGrey');
		}
		else
		{
			$(this).addClass('starGold');
			$(this).removeClass('starGrey');
		}
		
		var pid = $(this).attr('pid');
		$.ajax({
            url: "index_ajax.php",
            dataType: "json",
            data: { act: "client-add_to_fav", pid: pid }
		});
	});
	
	$('#ajaxLoginBtn').click(function(e){
		e.preventDefault();
		e.stopPropagation();
		loginAjax(e);
	});
	
	$("#usernameAjax, #passwordAjax").keypress(function(e) {
		if ( e.which == 13 ) {
			e.preventDefault();
			e.stopPropagation();
			loginAjax(e);
		}
	});
	
    //on ProgamAddPatient page open PDF file for printing in new tab
    $('#program_add_patient_form [type=submit]').click(function(){
        if($(this).is('.printpdf'))
            $(this).parents('form:first').attr('target', '_blank');
        else
            $(this).parents('form:first').removeAttr('target');
    });
	
	$('#forgotPass').click(function(){
		window.location = '/index.php?pag=forgotpass';
	});
});

function loginAjax(e)
{
	
		
	var username = $('#usernameAjax').val();
	var password = $('#passwordAjax').val();
	var store_login = $('#store_loginAjax').attr('checked') ? 'on' : '';

	$.ajax({
		url: "index_ajax.php",
		dataType: "json",
		data: { act: "auth-login_ajax", username: username, password: password, store_login: store_login },
		success: function(res)
		{
			if(!res.failure)
			{
				if(res.pag_redir)
					window.location = '/index.php?pag='+res.pag_redir;
			}
			else
			{
				alert(res.error);
				if(res.pag_redir)
					window.location = '/index.php?pag='+res.pag_redir;
			}
		}
	});
}