/**
 * @author shadow
 */

var inter=0;
var inter2=0;
var frame=1;
var slides=[];
var slide=0;
var sliderWidth=220;
var sliderDelay=10000;

window.onload=run;

function run(){
	var el=document.getElementById('sliders');
	for(var i=0;i< el.childNodes.length;i++){
		if(el.childNodes[i].nodeType==1){//div :D
			slides.push(el.childNodes[i]);
		}
	}
	document.getElementById('frame1').appendChild(slides[slide]);
	if(!!slides[slide+1]){	
		document.getElementById('frame2').appendChild(slides[slide+1]);
		slide++;
		inter=window.setInterval(init,sliderDelay);
	}
	
}


function init(){	
clearTimer();
/*	var el=document.getElementById('sliders');
	for(var i=0;i< el.childNodes.length;i++){
		if(el.childNodes[i].nodeType==1){//div :D
			slides.push(el.childNodes[i]);
		}
	}
	document.getElementById('frame1').appendChild(slides[slide]);
	document.getElementById('frame2').appendChild(slides[slide+1]);
	slide++;*/	
	inter = setInterval('animSlide()',50);	
};


function setAtEnd(el){
	var elm=el.cloneNode(false);
	elm.style.top=sliderWidth+'px';
	el.parentNode.insertBefore(elm,el.parentNode.lastChild.nextSibling);
	el.parentNode.removeChild(el);
}

function setAtStart(el){
	var elm=el.cloneNode(false);
	elm.style.top='0px';
	elm.setAttribute('id','new');
	el.parentNode.insertBefore(elm,el.parentNode.firstChild);
	el.parentNode.removeChild(el);
}

function getFrame(){
	if(frame==1){
		frame++;
	}else{
		frame=1;
	}
	return frame;
}

function nextFrame(){
	getFrame();
	var el=document.getElementById('frame'+frame);
	el.appendChild(slides[slide]);
	if(slide==slides.length-1){
		slide=0;
	}else{
		slide++;
	}
	el.nextSibling.appendChild(slides[slide]);
	clearTimeout(inter2);
	inter=setInterval('animSlide()',50);
}

var animSlide=function (){
	var elm = document.getElementById('frame'+frame);
	var elm1 = elm.nextSibling;
	var pos1 = elm.style.top == '' ? 0 : parseInt(elm.style.top)*-1;
	var pos2 = elm1.style.top == '' ? sliderWidth : parseInt(elm1.style.top);
	
	if(pos1==sliderWidth){
		clearInterval(inter);
		setAtEnd(elm);
		inter2=setTimeout('nextFrame()',sliderDelay);
	}else{
		elm.style.top = (pos1+10)*-1+'px';
		elm1.style.top = (pos2-10)+'px';
	}
}

var nxtAnimSlide=function (){
	var elm=document.getElementById('slides').firstChild;
	var elm1 = elm.nextSibling;
	var pos1 = elm.style.top == '' ? 0 : parseInt(elm.style.top)*-1;
	var pos2 = elm1.style.top == '' ? sliderWidth : parseInt(elm1.style.top);
	
	if(pos1==sliderWidth){
		clearInterval(inter);
		setAtEnd(elm);
	//	inter2=setTimeout('nextFrame()',sliderDelay);
	}else{
		elm.style.top = (pos1+10)*-1+'px';
		elm1.style.top = (pos2-10)+'px';
	}
}

var prevAnimSlide=function (){
	var elm = document.getElementById('slides').lastChild;
	var elm1 = elm.previousSibling;
	var pos1 = elm.style.top == '' ? 0 : parseInt(elm.style.top)*-1;
	var pos2 = elm1.style.top == '' ? sliderWidth : parseInt(elm1.style.top);
	
	if(pos1==sliderWidth*-1){
		clearInterval(inter);
	}else{
		elm.style.top = (pos1-10)*-1+'px';
		elm1.style.top = (pos2+10)+'px';
	}
}

function next(){
	clearTimer();
	var el = document.getElementById('slides').firstChild;
	el.appendChild(slides[slide]);
	if(slide == slides.length-1){
		slide=0;
	}else{
		slide++;
	}
	el.nextSibling.appendChild(slides[slide]);
	inter = setInterval('nxtAnimSlide()',50);	
}

function prev(){
	clearTimer();
	setAtStart(document.getElementById('slides').lastChild);
	var el = document.getElementById('slides').lastChild;
	el.appendChild(slides[slide]);
	if(slide == 0){
		slide=slides.length-1;
	}else{
		slide--;
	}
	el.previousSibling.appendChild(slides[slide]);
	inter = setInterval('prevAnimSlide()',50);	
}

function clearTimer(){
	clearInterval(inter);
	clearTimeout(inter2);
}