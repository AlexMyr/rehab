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
			}
		else if($("#exercise_id").attr('value')!="") $("form#exerciseAddForm")[0].submit();
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
								var details = parseInt($(this).attr('id').split('details_')[1]);
								e.stopPropagation();
								e.preventDefault();
						var body = $(document).width();
						var content = $('.siteBody').width();
						var margins = body-content;
						var size = $(this).offset();
						var thetop = size.top-79;
						var theleft = size.left-220;
						obj.css({
							'top':thetop,
							'right':'11px'
						});
								$('#text_'+details).toggleClass('displayBlock');
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
						var scrolledSize = $('#content').scrollTop();
						
						var details = parseInt($(this).attr('id').split('compactViewDetails_')[1]);
						e.stopPropagation();
						e.preventDefault();
						var body = $(document).width();
						var content = $('.siteBody').width();
						var margins = body-content;
						var size = $(this).offset();
						var thetop = size.top-165 + scrolledSize;
						var theleft = size.left-(margins/2)-355;
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
						var thumbpath = "phpthumb/phpThumb.php?src=../";
//						var thumbsize = "&amp;wl=50&amp;hp=50"; // this crashes
						var thumbsize = "&wl=64&hp=64";
						var exUL = $("#sortable");
						var exLI = $('<li/>')
							.attr('id', obj.innerHTML.PROGRAM_ID);
						var exLI_img = $('<img/>')
							.attr('alt', obj.innerHTML.PROGRAM_TITLE)
							.attr('title', obj.innerHTML.PROGRAM_TITLE)
							.attr('width', 64)
							.attr('height', 64)
							.attr('src',thumbpath + obj.innerHTML.PROGRAM_IMAGE + thumbsize);
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
						var exLI_delete = $('<a/>')
							.attr('id', 'del_'+obj.innerHTML.PROGRAM_ID)
							.attr('class','exercise_del')
							.attr('href','#');
						var exLI_details = $('<a/>')
							.attr('id', 'details_'+obj.innerHTML.PROGRAM_ID)
							.attr('class','exercise_details');
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
						exLI_details.html('details');
						exLI_text.appendTo(exLI);
						exLI_textNode.appendTo(exLI_text);
						exDIV_clear.appendTo(exLI);
						exLI.appendTo(exUL);

						exLI_delete.bind('click',function(e)
							{ 
								e.preventDefault(); 
								e.stopPropagation(); 
								delValue[obj.innerHTML.PROGRAM_ID] = obj.innerHTML.PROGRAM_ID;
								rmExercise(obj.innerHTML.PROGRAM_ID); 
							});
						
					}
			}
		
		makeSortable();
//		makeDelete();
		doExerciseDetails();
	}

// START THE DOCUMENT READY
$(document).ready(function() 
{
	
	$('.changeVideoUrl').click(function(){
		var newUrl = "http://www.youtube.com/v/"+$(this).attr('url')+"?version=3&f=videos&app=youtube_gdata&autoplay=0";
		$('#currentVideo').attr('src', newUrl);
	})
	
	posX = null;
	posY = null;
	redirect_url = null;
	
	$.post("index_ajax.php", { test: "test"}, function(data){
		if(data == 'error' || parseInt(data) == NaN)
		{
			jQuery(".changeURL a").each(function(){
				jQuery(this).attr('href', 'index.php?pag=profile');
			});
		}
	})
	
	$(document).bind('mousemove', function(e){
		if (typeof e == 'undefined')
			myEvent = window.event;
		else
			myEvent = e;
		posX = myEvent.clientX;
		posY = myEvent.clientY;
	});

 	$(window).bind('beforeunload', function() {
 	 	if(jQuery('input[name="act"]').val()=='client-update_exercise_plan')
		{
			if(posY == null || posY <= 25)
			{
				redirect_url = jQuery('.preview_buttons .moreBtn:first').attr('href');
			}
			else
				redirect_url = null;
		}
		else 	if(jQuery('input[name="act"]').val()=='client-update_exercise')
		{
			if(posY == null || posY <= 25)
			{
				redirect_url = 'index.php?pag=dashboard';
			}
			else
				redirect_url = null;
		}
	});
		
	$(window).bind('unload', function() {
		if(jQuery('input[name="act"]').val()=='client-update_exercise_plan' && redirect_url != null)
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
	var hovered = false;
	function show_submenu(obj)
	{
      hovered = false;
		if($(obj).attr('href') == 'index.php?pag=profile')
		{
            hovered = true;
			$(obj).addClass('topForSubMenu');
            var parOffset = $(obj).offset();
            var parHeight = $(obj).css('height');
            $('#submenuList').css('top', parOffset.top+parseInt(parHeight)+2+'px');
            $('#submenuList').css('left', parOffset.left+'px');
			$('#submenuList').css('display', 'block');
		}
	}

	function hide_submenu()
	{
      if(hovered)
      {
		$('.item1').removeClass('topForSubMenu');
		$('#submenuList').css('display', 'none');
      }
	}
	
	$('.navMenu .item1').hover(function(){
		show_submenu(this);
		}, function(){
		  if(hovered)
		  {
			//hovered = false;
			setTimeout(hide_submenu, 100);
		  }
		  else
			hovered = true;
		});
		
		$('#submenuList').hover(
		function(){
			hovered = false;
		},
		function(){
			hovered = true;
			setTimeout(hide_submenu, 100);
		}
	);
		
	$('.navMenu .item1, .footer .item1').click(function(e){
		if($(this).attr('href').indexOf('index.php?pag=login&act=auth-logout')>-1)
		{
			FB.logout();
			//console.log("logout");
		}
	});
	
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
	
	$('.item img, .itemCompact img').click(function(){
		var clickedImgUrl = $(this).attr('src');
		clickedImgUrl = clickedImgUrl.match(/([\w]+?)\.jpg/);
		var lightBox = $('<div id="innerLightBoxDiv"><img src="phpthumb/phpThumb.php?src=../upload/'+clickedImgUrl[1]+'.jpg&wl=300&hp=300" /></div>');
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
		$(".scrolledList").scrollTop(0);
		$(window).scrollTop(0);
	});
	
	$('.changePaymentRadio').click(function(){
		var defaultValue = "index.php?pag=profile_do_payment&price_id=";
		var paymentUrl = defaultValue + $(this).val();
		$('#submitPayment').attr('href', paymentUrl).data('planChosen', true);
	});
	
	$('#submitPayment').click(function(e){
		e.preventDefault();
		if($(this).data('planChosen')!=true)
		{
			alert('You already has trial price plan.');
			return false;
		}
		else
			window.location = $(this).attr('href');
	});
	
	$('.showLimitError').click(function(e){
		e.preventDefault();
		if(confirm("Trial user can't add more than 5 programs. Do you want to upgrade your plan?"))
		{
			window.location = 'index.php?pag=profile_payment';
		}
		else
		{
			return false;
		}
	});
	
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
			e.stopPropagation();	
			e.preventDefault();
			doSave();
		});
		makeSortable();
		makeDelete();
		doExerciseDetails();
		doExerciseCompactViewDetails();
		
		$('.lang a').click(function(){
			var lang = $(this).attr('value');
			document.cookie = 'language' + "=" + escape(lang) + "; expires=" + new Date( 2020, 1, 1 ) +  "; path=/";
		})
	});