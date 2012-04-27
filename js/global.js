//function used to determin the element on which an event took place
var getTargetElement=function(e){
	if(!e) var e=window.event;
	if (e.target) targ = e.target;
	else if (e.srcElement) targ = e.srcElement;
	if (targ.nodeType == 3) // defeat Safari bug
		targ = targ.parentNode;
	return targ;
}
//alias for document.getElementById with multiple selection option
function $() {
  var elements = new Array();

  for (var i = 0; i < arguments.length; i++) {
    var element = arguments[i];
    if (typeof element == 'string')
      element = document.getElementById(element);

    if (arguments.length == 1) 
      return element;

    elements.push(element);
  }

  return elements;
} 
//show a hidden element
function showElement(el){
	$(el).style.display='block';
}
//hide an element
function hideElement(el){
	$(el).style.display='none';
}
//toggle betwean show and hide
function toggle(pic,id){
	var preImg=new Image();
	preImg.src='img/minus.gif';
	var el=$(id);
	if(el.style.display=='' || el.style.display=='none'){
			showElement(id);
			$(pic).src='img/minus.gif';
	}else{
		hideElement(el); 
		$(pic).src='img/plus.gif';
	}	
}
//execute func onload
function addLoadEvent(func){
	if(window.addEventListener)	
	{
		window.addEventListener("load",func,false);
	}else{
		window.attachEvent("onload",func);	
	}
	return true;	
}

function clearAct(){
	var el=document.getElementById('act');
	var el2=document.getElementById('act2');
	el2.value=el.value;
	el.value='';
	return true;
}


//colorize table with class="greyMaker"
var grayMaker={
	'colorOne':'#F8F9FA',
	'colorTwo':'#FFFFFF',
	'rowCount': 0,
	'colorize':function(el){
		if((grayMaker.rowCount%2)==0){		
			el.style.backgroundColor=grayMaker.colorOne;
		}else{
			el.style.backgroundColor=grayMaker.colorTwo;
		}			
	},
	'getTRChildNodes':function(el){
		var children=el.childNodes;
		var childrenCount=children.length;
		var foo=0;
		for(k=0;k<childrenCount;k++){
			if(children[k].tagName=='TD'){
					grayMaker.colorize(children[k]);
			}			
		}
	},
	'getTABLEChildNodes':function(el){
		var children=el.childNodes;
		var subChildren=null;
		for(var i=0;i<children.length;i++){
			if(children[i].hasChildNodes()){
				subChildren=children[i].childNodes;
				for(var j=0;j<subChildren.length;j++){
					if(subChildren[j].tagName=='TR'){
						grayMaker.getTRChildNodes(subChildren[j]);
						grayMaker.rowCount++;
					}
				}
			}				
		}	
	},
    'getTABLE':function(){
		var el=document.getElementsByTagName('TABLE');		
		for(i=0; i<el.length;i++){					
		var temp = new Array();
		temp = el[i].className.split(' ');
			if(temp[0]=='greyMaker'){
				if($('colors')){
					var tmp = grayMaker.colorOne;
					grayMaker.colorOne = grayMaker.colorTwo;
					grayMaker.colorTwo = tmp;
				}
				grayMaker.rowCount=0;
				grayMaker.getTABLEChildNodes(el[i]);		
			}
		}
	}
};